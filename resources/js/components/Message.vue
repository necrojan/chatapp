<template>
    <div class="container">
        <div class="row rounded-lg overflow-hidden shadow">
            <div class="col-5 px-0">
                <div class="bg-white user-sidebar">
                    <div class="bg-gray px-4 py-2 bg-light">
                        <p class="h5 mb-0 py-1">Users</p>
                    </div>
                    <div class="messages-box">
                        <div class="list-group rounded-0">
                            <a
                                    class="list-group-item list-group-item-action list-group-item-light rounded-0 friend"
                                    :class="{ 'active': (activeFriend == friend.id) }"
                                    v-for="friend in friends"
                                    :key="friend.id"
                                    @click="activeFriend = friend.id"
                            >
                                <div class="media">
                                    <div class="media-body ml-4">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <h6 class="mb-0">{{ friend.name }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-7 px-0">
                <div class="px-4 py-5 chat-box bg-white" ref="chatbox">
                    <div
                            v-for="message in allMessages"
                            class="media w-50 mb-3"
                            :class="{ 'ml-auto':  message.receiver_id != user.id}"
                    >
                        <div class="media-body">
                            <div
                                    class="rounded py-2 px-3 mb-2"
                                    :class="[ message.user_id == user.id ? 'bg-green-chat' : 'bg-light' ]"
                            >
                                <p
                                        class="text-small mb-0"
                                        :class="[ message.user_id == user.id ? 'text-white' : 'text-muted' ]"
                                >{{ message.message }}</p>
                            </div>
                            <p class="small text-muted">{{ $date.format(message.updated_at, 'LT') }} | {{
                                $date.format(message.updated_at, 'MMMM DD') }}</p>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-1 spinner-container" v-if="typingFriend.name">{{ typingFriend.name }} is typing
                        <span class="spinner">
                          <div class="bounce1"></div>
                          <div class="bounce2"></div>
                          <div class="bounce3"></div>
                        </span>
                    </div>
                <div class="input-group">
                    <input
                            v-on:keyup.enter="submitMessage"
                            v-model="message"
                            @keydown="notifyPeers"
                            type="text"
                            placeholder="Type a message" aria-describedby="button-addon2"
                            class="form-control rounded-0 border-0 py-4 bg-light"
                    >
                    <div class="input-group-append">
                        <button
                                @click="submitMessage"
                                id="button-addon2"
                                class="btn btn-link"><i
                                class="fa fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['user'],
        data() {
            return {
                message: null,
                allMessages: [],
                activeFriend: null,
                typingFriend: {},
                typeClockVar: null,
                users: [],
                userAgent: null,
                accepted: false
            }
        },

        created() {
            this.getAllUsers();

            Echo.private('message.' + this.user.id)
                .listen('NewMessage', e => {
                    this.typingFriend = {};
                    this.activeFriend = e.message.user_id;
                    this.allMessages.push(e.message);
                    this.scrollToBottom();
                })
                .listenForWhisper('typing', e => {
                    if (e.user.id == this.activeFriend) {
                        this.typingFriend = e.user;
                        if (this.typeClockVar) clearTimeout(this.typeClockVar);
                        this.typeClockVar = setTimeout(() => {
                            this.typingFriend = {};
                        }, 3000);
                    }
                });
        },

        computed: {
            friends() {
                return this.users.filter((user) => {
                    return this.user.id != user.id
                });
            },
        },
        watch: {
            activeFriend(val) {
                this.getMessage();
            }
        },
        methods: {
            getMessage() {
                axios.get('/api/private/' + this.activeFriend).then(res => {
                    this.allMessages = res.data;
                    this.scrollToBottom();
                });
            },

            getAllUsers() {
                axios.get('/api/users').then(res => {
                    this.users = res.data;
                    this.activeFriend = this.friends.length > 0 ? this.friends[0].id : null;
                });
            },

            submitMessage() {
                let payload = {
                    receiver_id: this.activeFriend,
                    message: this.message,
                };

                axios.post('/api/private', payload).then(res => {
                    this.allMessages.push(res.data.message);
                    this.scrollToBottom();

                    this.message = '';
                });
            },

            notifyPeers() {
                Echo.private('message.' + this.activeFriend)
                    .whisper('typing', {
                        user: this.user
                    });
            },

            scrollToBottom() {
                this.$nextTick(() => this.$refs.chatbox.scrollTop = 9999);
            }
        }
    }
</script>

<style>
    .friend {
        cursor: pointer;
        list-style: none;
    }

    .friend.active {
        background: #173A64 !important;
        border: none;
    }

    .user-sidebar {
        background: #221E1A !important;
    }

    .user-sidebar a {
        color: whitesmoke;
    }

    .bg-green-chat {
        background: #98CA41;
    }

    .chat-box {
        background: #e6eaea !important;
    }

    .list-group-item {
        background: #221E1A !important;
    }

    .spinner {
        margin: 100px auto 0;
        width: 70px;
        text-align: center;
    }

    .spinner > div {
        width: 8px;
        height: 8px;
        background-color: #333;

        border-radius: 100%;
        display: inline-block;
        -webkit-animation: sk-bouncedelay 1.4s infinite ease-in-out both;
        animation: sk-bouncedelay 1.4s infinite ease-in-out both;
    }

    .spinner-container {
        background: transparent;
        height: 50px;
        position: absolute;
        bottom: 40px;
        left: 0;
    }

    .spinner .bounce1 {
        -webkit-animation-delay: -0.32s;
        animation-delay: -0.32s;
    }

    .spinner .bounce2 {
        -webkit-animation-delay: -0.16s;
        animation-delay: -0.16s;
    }

    @-webkit-keyframes sk-bouncedelay {
        0%, 80%, 100% {
            -webkit-transform: scale(0)
        }
        40% {
            -webkit-transform: scale(1.0)
        }
    }

    @keyframes sk-bouncedelay {
        0%, 80%, 100% {
            -webkit-transform: scale(0);
            transform: scale(0);
        }
        40% {
            -webkit-transform: scale(1.0);
            transform: scale(1.0);
        }
    }
</style>
