<template>
    <div :class="{ 'container clientMargin' : this.role[0].name == 'client', 'mx-2 px-2' : hasRoleAdmin }">
        <div class="stand-by" v-if="standBy">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="text-center mt-5">Please stand by while
                        <br>we connect you
                        to an available technician
                        <span class="spinner">
                      <div class="bounce1"></div>
                      <div class="bounce2"></div>
                      <div class="bounce3"></div>
                    </span>
                    </h1>
                </div>
            </div>
        </div>

        <div v-else class="row rounded-lg overflow-hidden shadow">
            <div class="col-md-4  px-0" v-if="role[0].name !== 'client'">
                <div class="bg-white user-sidebar">
                    <div class="messages-box">
                        <div class="list-group rounded-0">
                            <div class="queue">
                                <h5 class="pool px-3 py-2 text-white border-bottom border-light mb-0"><strong>My Chat</strong></h5>
                                <a
                                        class="list-group-item list-group-item-action list-group-item-light rounded-0 friend"
                                        :class="{ 'active':  (activeFriend == friend.user_id) }"
                                        @click="setActiveIndex(friend.user_id)"
                                        v-for="friend in friends"
                                        :key="friend.id"
                                >
                                    <div class="user-wrapper">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mr-2 float-left">
                                                    <div class="dot"
                                                        :class="[activeUsers.some((ele) => {
                                                            if (ele.user) {
                                                                return ele.id === friend.id;
                                                            }
                                                        }) ? 'isActive' : 'inActive']"
                                                    ></div>
                                                </div>
                                                <h6 class="mb-0 text-white-50">
                                                    <div class="icon-info d-inline">
                                                        <div class="tip">
                                                            <p class="mb-1">User-Agent: {{ friend.machine.user_agent }}</p>
                                                            <p>Ip: {{ friend.machine.ip }}</p>
                                                        </div>
                                                    </div>
                                                    {{ friend.user.name }}
                                                </h6>
                                                <small class="text-white-50 d-block">{{ friend.user.email }}</small>
                                                <small class="text-white-50 d-block pl-3">{{ friend.company }}</small>
                                                <div :id="friend.id + '-not-verified'">
                                                    <small
                                                            :class="[ !friend.is_verified ? 'not-verified' : 'verified' ]"
                                                            class="text-white-50 verified-toggle-class">
                                                        {{ !friend.is_verified ? 'Not verified' : 'Verified' }}
                                                    </small>
                                                    <div v-if="!friend.is_verified" class="d-inline"
                                                         :id="friend.id + '-not-verified\''">
                                                        <small class="text-white-50">|</small>
                                                        <small class="verification text-white-50"
                                                               @click="sendVerification(friend.user.id)">Send
                                                            verification</small>
                                                    </div>
                                                </div>
                                                <div class="d-none" :id="friend.id + '-verified'">
                                                    <small class="text-white-50 verified">Verified</small>
                                                </div>

                                            </div>
                                            <div class="col-md-6">
                                                <button type="button" class="btn btn-primary float-right"
                                                        @click="addToPool(friend)">Add to Pool
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <p v-if="friends.length === 0" class="text-white-50 px-3 py-2 mb-0">No Available chat</p>
                            </div>
                            <div class="pool">
                                <h5 class="pool px-3 py-2 text-white border-bottom border-light mb-0">Pool</h5>
                                <a
                                        class="list-group-item list-group-item-action list-group-item-light rounded-0 friend"
                                        v-for="client in pools"
                                        :key="client.id"
                                >
                                    <div class="user-wrapper">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mr-2 float-left">
                                                    <div class="dot"
                                                         :class="[activeUsers.some((ele) => {
                                                             if (ele.user) {
                                                                 return ele.id === client.id;
                                                             }
                                                         }) ? 'isActive' : 'inActive']"
                                                    ></div>
                                                </div>
                                                <h6 class="mb-0 text-white">{{ client.user.name }}</h6>
                                                <small class="text-white d-block pl-3">{{ client.company }}</small>
                                                <small class="text-white d-block pl-3">{{ client.user.email }}</small>
                                            </div>
                                            <div class="col-md-6">
                                                <button type="button" class="btn btn-primary float-right"
                                                        @click="removeFromPool(client.id)">Pick up
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <p v-if="pools.length === 0" class="text-white-50 px-3 py-2 mb-0">No available clients on pool</p>
                            </div>
                            <div class="active-users">
                                <h5 class="pool px-3 py-2 text-white border-bottom border-light mb-0">Active Users</h5>

                                <a class="list-group-item list-group-item-action list-group-item-light rounded-0 friend"
                                   v-for="client in activeUsers"
                                   :key="client.id"
                                   v-if="exceptAdmin(client)"
                                >
                                    <div class="user-wrapper">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mr-2 float-left">
                                                    <div class="dot"></div>
                                                </div>
                                                <h6 class="mb-0 text-white">{{ client.user.name }}</h6>
                                                <small class="text-white">{{ client.user.email }}</small>
                                                <p class="text-white" :id="'active-' + client.user.id">{{ getClientCookie(client) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <p v-if="!activeUsersExist" class="text-white-50 px-3 py-2 mb-0">No active users</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="px-0"
                 :class="isFullCol">
                <div class="px-4 py-5 chat-box bg-white" ref="chatbox" >
                    <div
                            v-for="message in allMessages"
                            class="media w-50 mb-3"
                            :class="{ 'ml-auto':  message.receiver_id != user.id}"
                    >
                        <div class="media-body w-100">
                            <div
                                    class="rounded py-2 px-3 mb-2"
                                    :class="[ message.user_id == user.id ? 'bg-green-chat' : 'bg-light' ]"
                            >
                                <p
                                        class="text-small mb-0"
                                        :class="[ message.user_id == user.id ? 'text-white' : 'text-muted' ]"
                                        v-linkified

                                >{{ message.message }}</p>
                            </div>
                            <img v-if="allowedImage(message.message)"
                                 :src="'/storage/messages/images/' + message.message"
                                 :alt="message.message"
                                 class="image-message"
                            >
                            <a v-else-if="allowedFiles(message.message)" download :href="'/storage/messages/images/' + message.message">{{ message.message }}</a>
                            <p class="small mb-1 text-muted">{{ $formatter.capitalizeText(message.user.name) }}</p>
                            <p class="small mb-1 text-muted">{{ $formatter.dateFormat(message.updated_at, 'LT') }} | {{
                                $formatter.dateFormat(message.updated_at, 'MMMM DD') }}</p>
                        </div>
                    </div>
                </div>
                <div
                        :class="adjustBottom"
                        class="px-4 py-1 spinner-container"
                        v-if="typingFriend.name">{{
                    $formatter.capitalizeText(typingFriend.name) }} is typing
                    <span class="spinner">
                      <div class="bounce1"></div>
                      <div class="bounce2"></div>
                      <div class="bounce3"></div>
                    </span>
                </div>

                <div class="card mb-0">
                    <div class="card-body p-3">
                        <div class="input-group">
                            <auto-suggest
                                    v-if="hasRoleAdmin"
                                    v-on:resultSelected="resultSelected"
                                    ref="autoSuggest"
                                    :show="showSuggestion"
                                    :results="cannedResults"
                                    :counter="arrowCounter"
                                    v-closable="{
                                        exclude: ['button', 'pinButton', 'personalButton'],
                                        handler: 'onClose'
                                    }"
                            ></auto-suggest>
                            <input
                                    @keyup="getCannedResponse"
                                    @keyup.enter="submitMessage"
                                    v-model="message"
                                    @keydown="notifyPeers"
                                    @keydown.down="onArrowDown"
                                    @keydown.up="onArrowUp"
                                    @keydown.tab="onTab"
                                    @drop.prevent="addFile"
                                    @dragover.prevent="onDragOverFiles"
                                    @dragleave.prevent="onDragLeaveFiles"
                                    type="text"
                                    placeholder="Type a message" aria-describedby="button-addon2"
                                    class="form-control rounded-0 py-4 bg-light"
                            >
                            <div v-if="fileSaving" class="file-saving">
                                <div class="file-saving__wrapper">
                                    <h3 class="mb-2">Uploading Files... {{ uploadPercentage }} %</h3>
                                    <b-progress :value="uploadPercentage" :max="max" variant="success"></b-progress>
                                </div>
                            </div>
                            <div class="input-group-append">
                                <button
                                        :disabled="!message"
                                        @click="submitMessage"
                                        id="button-addon2"
                                        class="btn btn-link"><i
                                        class="fa fa-paper-plane"></i>
                                </button>
                            </div>
                        </div>
                        <div v-if="hasRoleAdmin" class="icons-wrapper">
                            <hr>
                            <i class="icon-energy float-left" @click="showModal = true" ref="button"></i>
                            <i class="icon-pin float-left pl-2" @click="showPin = true" ref="pinButton"></i>
                            <i class="icon-speech float-left pl-2" @click="showPersonal = true" ref="personalButton"></i>
                            <i class="fa fa-arrows-alt-h float-left pl-2" @click="teamViewer()"></i>
                            <file-upload
                                    v-on:imagename="getImageName"
                                    :activeFriend="activeFriend"
                            ></file-upload>
                            <pins
                                    v-on:pinmessage="getPinMessage"
                                    :show="showPin"
                                    @close="showPin = false"
                                    v-closable="{
                                    exclude: ['button', 'pinButton', 'personalButton'],
                                    handler: 'onClose'
                                }"
                            ></pins>
                            <responses
                                    v-on:responsetext="getResponseText"
                                    :show="showModal"
                                    @close="showModal = false"
                                    v-closable="{
                                exclude: ['button', 'pinButton', 'personalButton'],
                                handler: 'onClose'
                            }"
                            >
                            </responses>
                            <personal-responses
                                    v-on:personalresponsetext="getPersonalResponseText"
                                    :show="showPersonal"
                                    @close="showPersonal = false"
                                    v-closable="{
                                exclude: ['button', 'pinButton', 'personalButton'],
                                handler: 'onClose'
                                }"
                            ></personal-responses>
                        </div>
                        <div v-else class="icons-wrapper">
                            <hr>
                            <file-upload
                                    v-on:imagename="getImageName"
                                    :activeFriend="activeFriend"
                            ></file-upload>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    import Vue from 'vue';
    import Swal from 'sweetalert2';

    const FILE_SAVING = 1;
    const FILE_SUCCESS = 2;
    const FILE_ERROR = 3;
    // This variable will hold the reference to
    // document's click handler
    let handleOutsideClick;

    Vue.directive('closable', {
        bind(el, binding, vnode) {
            // Here's the click/touchstart handler
            // (it is registered below)
            handleOutsideClick = (e) => {
                e.stopPropagation()
                // Get the handler method name and the exclude array
                // from the object used in v-closable
                const {handler, exclude} = binding.value

                // This variable indicates if the clicked element is excluded
                let clickedOnExcludedEl = false
                exclude.forEach(refName => {
                    // We only run this code if we haven't detected
                    // any excluded element yet
                    if (!clickedOnExcludedEl) {
                        // Get the element using the reference name
                        const excludedEl = vnode.context.$refs[refName]
                        // See if this excluded element
                        // is the same element the user just clicked on
                        clickedOnExcludedEl = excludedEl.contains(e.target)
                    }
                })

                // We check to see if the clicked element is not
                // the dialog element and not excluded
                if (!el.contains(e.target) && !clickedOnExcludedEl) {
                    // If the clicked element is outside the dialog
                    // and not the button, then call the outside-click handler
                    // from the same component this directive is used in
                    vnode.context[handler]()
                }
            }
            // Register click/touchstart event listeners on the whole page
            document.addEventListener('click', handleOutsideClick)
            document.addEventListener('touchstart', handleOutsideClick)
        },

        unbind() {
            // If the element that has v-closable is removed, then
            // unbind click/touchstart listeners from the whole page
            document.removeEventListener('click', handleOutsideClick)
            document.removeEventListener('touchstart', handleOutsideClick)
        }
    });


    export default {
        props: ['user', 'role'],
        data() {
            return {
                message: null,
                allMessages: [],
                activeFriend: null,
                typingFriend: {},
                typeClockVar: null,
                users: [],
                userAgent: null,
                accepted: false,
                pools: [],
                activeIndex: null,
                standBy: false,
                activeUsers: [],
                acceptedBy: null,
                showModal: false,
                showPin: false,
                showPersonal: false,
                showSuggestion: false,
                cannedResults: [],
                cannedResultsClocked: null,
                arrowCounter: -1,
                teamLink: 'https://get.teamviewer.com',
                uploadPercentage: 0,
                max: 100,
                currentStatus: null,
                out: false,
            }
        },

        created() {
            if (this.hasRoleAdmin) {
                this.getPoolUsers();
                this.getClientsNotInPool();
                this.$bus.$on(['REMOVE_FROM_POOL'], () => {
                    this.getClientsNotInPool();
                    this.getPoolUsers();
                });
            }

            this.displayStandBy();

            Echo.channel('add.queue')
                .listen('AddToQueue', e => {
                    this.users.push(e.client);
                });

            Echo.join('restore.pool')
                .here((users) => {
                    this.activeUsers = users;
                })
                .joining((user) => {
                    this.activeUsers.push(user);
                    if (this.hasRoleAdmin) this.playNotification('notify_pool');
                })
                .leaving((user) => {
                    this.activeUsers.splice(this.activeUsers.indexOf(user), 1);
                    if (this.hasRoleAdmin) {
                        this.out = true;
                    }
                })
                .listen('RestorePool', e => {
                    if (this.hasRoleAdmin) {
                        if (!this.pools.some(client => client.id === e.client.id)) {
                            this.pools.push(e.client);
                            this.playNotification('notify_pool');
                        }
                        this.users.splice(this.users.findIndex(ele => ele.id === e.client.id), 1);

                        const user = this.activeUsers.find((activeUser) => {
                            if (activeUser.user && e.client.user) {
                                return activeUser.user.id === e.client.user.id
                            }
                        });
                        if (user) {
                            const element = document.getElementById('active-' + user.user.id);
                            element.innerHTML = '';
                        }
                        this.$cookie.delete(e.client.user.id + 'accepted-by');
                    }

                    if (this.user.id === e.client.user_id) {
                        this.standBy = true;
                    }
                });

            Echo.channel('remove.pool')
                .listen('RemovePool', e => {
                    this.pools.splice(this.pools.findIndex(ele => ele.id === e.client.id), 1);
                    this.users.push(e.client);
                    this.activeFriend = e.client.accepted_by;
                    if (this.user.id === e.client.user_id) {
                        this.standBy = false;
                    }
                });

            Echo.channel('newpooluser')
                .listen('NewPool', e => {
                    if (!this.pools.some(client => client.id === e.client.id)) {
                        this.pools.push(e.client);
                    }
                });

            Echo.channel('accepted.by')
                .listen('AcceptedBy', e => {
                    if (this.hasRoleAdmin) {
                        const user = this.activeUsers.find((activeUser) => {
                            if (activeUser.user && e.client.user) {
                                return activeUser.user.id === e.client.user.id
                            }
                        });

                        if (user) {
                            const element = document.getElementById('active-' + user.user.id);
                            element.innerText = e.user.name;
                            this.$cookie.set(user.user.id + 'accepted-by', e.user.name);
                        } else {
                            if (e.client.user) {
                                this.$cookie.set(e.client.user.id + 'accepted-by', e.user.name);
                            }
                        }
                    }
                });

            Echo.private('message.' + this.user.id)
                .listen('NewMessage', e => {
                    this.typingFriend = {};
                    this.activeFriend = e.message.user_id;
                    this.allMessages.push(e.message);
                    this.scrollToBottom();
                    if (this.hasRoleAdmin) this.playNotification('notify_message');
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

            Echo.private('verify.' + this.user.id)
                .listen('ClientVerification', e => {
                    if (e.user.id === this.user.id) {
                        Swal.fire({
                            title: 'Please input your code',
                            input: 'text',
                            placeholder: 'Enter your code'
                        }).then((res) => {
                            if (res.value) {
                                axios.post('/verify/' + this.user.id, {code: res.value});
                            }
                        })
                    }
                });

            Echo.channel('verified')
                .listen('ClientVerified', e => {
                    if (e.client) {
                        let user = e.client;
                        const index = this.friends.findIndex((ele) => {
                            ele.id === user.id;
                        });
                        if (index) {
                            const eleNotVerified = document.getElementById(`${user.id}-not-verified`);
                            const eleVerified = document.getElementById(`${user.id}-verified`);
                            eleNotVerified.style.display = 'none';
                            eleVerified.setAttribute('style', 'display: block !important');
                        }
                    }

                });
        },

        mounted() {
            if (!this.hasRoleAdmin) {
                this.getAcceptedBy();
            }
        },

        computed: {
            friends() {
                return this.users.filter((user) => {
                    return this.user.id !== user.user_id && this.user.id === user.accepted_by
                });
            },

            isFullCol() {
                return this.role[0].name === 'client' ? 'col-md-12' : 'col-md-8';
            },

            hasRoleAdmin() {
                return this.role[0].name === 'admin' || this.role[0].name === 'agent';
            },

            adjustBottom() {
                return this.hasRoleAdmin ? 'bottomForAdmin' : 'bottomForClient';
            },

            activeUsersExist() {
                return this.activeUsers.some((user) => {
                    return user.user && user.client !== null;
                })
            },

            fileSaving() {
                return this.currentStatus === FILE_SAVING;
            }
        },
        watch: {
            activeFriend(val) {
                if ((this.role[0].name === 'admin' || this.role[0].name === 'agent') && this.friends.length == 0) {
                    this.activeFriend = null;
                    this.$cookie.delete('activeIndex');
                } else if (this.role[0].name === 'admin' || this.role[0].name === 'agent') {
                    this.getMessage(this.activeFriend);
                }
            }
        },
        methods: {
            getMessage() {
                if (typeof this.activeFriend == "number") {
                    axios.get('/api/private/' + this.activeFriend).then(res => {
                        this.allMessages = res.data;
                        this.scrollToBottom();
                    });
                }

            },

            getPoolUsers() {
                axios.get('/api/pools').then(res => {
                    this.pools = res.data;
                });
            },

            submitMessage() {
                if (!this.activeFriend) {
                    const toast = this.$sweet.toast('top-center', false, 3000);
                    toast.fire({
                        title: 'Oops',
                        text: 'Must select a client!',
                        type: 'error'
                    });
                    return;
                }

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
                if (this.$refs.chatbox) {
                    this.$nextTick(() => this.$refs.chatbox.scrollTop = 9999);
                }
            },

            removeFromPool(clientId) {
                axios.post('/api/pool/' + clientId, {
                    userId: this.user.id
                }).then(() => {
                    this.$bus.$emit('REMOVE_FROM_POOL');
                });
                this.activeFriend = clientId;
            },

            addToPool(client) {
                axios.post('/api/pool/' + client.id + '/add').then(res => {
                });
            },

            getClientsNotInPool() {
                axios.get('/api/clients').then(res => {
                    this.users = res.data;
                    if (!this.$cookie.get('activeIndex')) {
                        this.activeFriend = this.friends.length > 0 ? this.friends[0].user_id : null;
                    } else {
                        this.activeFriend = this.$cookie.get('activeIndex');
                    }
                })
            },

            setActiveIndex(receiverId) {
                this.activeIndex = receiverId;
                this.$cookie.delete('activeIndex');
                this.$cookie.set('activeIndex', receiverId);
                this.activeFriend = receiverId;
            },

            displayStandBy() {
                let vm = this;
                vm.standBy = vm.role[0].name === 'client'
                    && vm.user.client.accepted_by === null;

            },

            exceptAdmin(client) {
                if (client.user && client.user.id !== this.user.id) {
                    return true;
                }
                return false;
            },

            getClientCookie(client) {
                if (client.user) {
                    return this.$cookie.get(client.user.id + 'accepted-by');
                }
            },

            getResponseText(value) {
                this.message = value;
            },

            getPersonalResponseText(value) {
                this.message = value;
            },

            getPinMessage(value) {
                this.message = value;
            },

            allowedImage(value) {
                const allowedImage = /(\.jpg|\.jpeg|\.png|\.gif)$/i;

                return allowedImage.exec(value);
            },

            allowedFiles(value) {
                const allowedFiles = /(\.doc|\.pdf|\.docx|\.txt)$/i;

                return allowedFiles.exec(value);
            },

            getImageName(values) {
                this.allMessages.push(values);
            },

            resultSelected(value) {
                this.showSuggestion = false;
                this.cannedResults = [];
                this.message = value;
            },

            getCannedResponse(e) {
                if (this.hasRoleAdmin) {
                    let params = {
                        search: e.target.value
                    };

                    if (this.message !== null && this.message.length > 1) {
                        this.showSuggestion = true;
                        axios.get('/search', {params: params}).then((res) => {
                            this.cannedResults = res.data;
                            if (this.cannedResults.length === 0) {
                                if (this.cannedResultsClocked) clearTimeout(this.cannedResultsClocked);
                                this.cannedResultsClocked = setTimeout(() => {
                                    this.showSuggestion = false;
                                    this.cannedResults = [];
                                }, 3000);
                            }
                        });

                    }
                    if (this.message !== null && this.message.length === 0) {
                        this.cannedResults = [];
                        this.showSuggestion = false;
                    }
                }

            },

            sendPin(text) {
                this.message = text;
            },

            onClose() {
                this.showModal = false;
                this.showPin = false;
                this.showSuggestion = false;
                this.showPersonal = false;
            },

            onArrowDown() {
                if (this.arrowCounter < this.cannedResults.length) {
                    this.arrowCounter = this.arrowCounter + 1;
                }
            },

            onArrowUp() {
                if (this.arrowCounter > 0) {
                    this.arrowCounter = this.arrowCounter - 1;
                }
            },

            onTab() {
                if (this.message != null) {
                    if (this.arrowCounter != -1) {
                        this.message = this.cannedResults[this.arrowCounter].message;
                        this.arrowCounter = -1;
                        this.showSuggestion = false;
                        this.cannedResults = [];
                    }
                }
            },

            sendVerification(clientId) {
                const toast = this.$sweet.toast('top-end', false, 3000);
                axios.get('/verify/' + clientId).then(res => {
                    toast.fire({
                        title: 'Great!',
                        text: 'Client has been notified!',
                        type: 'success'
                    });
                }).catch((e) => {
                    toast.fire({
                        title: 'Oops!',
                        text: 'something went wrong.',
                        type: 'error'
                    });
                });
            },

            getAcceptedBy() {
                axios.get('/api/acceptedBy').then(res => {
                    this.activeFriend = res.data;
                    this.getMessage();
                })
            },

            teamViewer() {
                this.message = this.teamLink;
            },

            async addFile(e) {
                const files = e.dataTransfer.files;
                const formData = new FormData();
                e.target.style.borderColor = '#ced4da';

                try {
                    for (let i = 0; i < files.length; i++) {
                        formData.append('attachments[]', files[i]);
                    }

                    let config = {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        },
                        onUploadProgress: (progressEvent) => {
                            this.uploadPercentage = Math.round(progressEvent.loaded * 100 / progressEvent.total);
                        }
                    };
                    this.currentStatus = FILE_SAVING;
                    const upload = await axios.post('/api/file-upload', formData, config);

                    if (upload.data.success) {
                        this.currentStatus = FILE_SUCCESS;
                        for (let i = 0; i < files.length; i++) {
                            const success = await axios.post('/api/private', {
                                receiver_id: this.activeFriend,
                                message: files[i].name
                            });
                            if (success) this.allMessages.push(success.data.message);
                        }

                        await Swal.fire({
                            title: 'Success!',
                            text: 'File was uploaded!',
                            icon: 'success',
                            showConfirmButton: false
                        });
                    }
                } catch (e) {
                    this.currentStatus = FILE_ERROR;
                    await Swal.fire({
                        title: 'Oops!',
                        text: 'File(s) cannot be uploaded!',
                        icon: 'error',
                        showConfirmButton: false
                    });
                }
            },

            onDragOverFiles(e) {
                e.target.style.borderColor = '#9DCC3C';
            },

            onDragLeaveFiles(e) {
                e.target.style.borderColor = '#ced4da';
            },

            playNotification(elementName) {
                let idName = '';
                if (elementName === 'notify_message') {
                    idName = 'notify_message';
                } else if (elementName === 'notify_pool') {
                    idName = 'notify_pool';
                }
                let audioElement = document.getElementById(idName);
                audioElement.play();
            }
        }
    }
</script>
<style>
    .icons-wrapper .pin {
        cursor: pointer;
    }

    .image-message {
        width: 50%;
    }
</style>