<template>
    <div class="modal-mask" @click="close" v-show="show" >
        <div class="modal-wrapper">
            <div class="modal-container" @click.stop>
                <div class="modal-header text-center">
                    <h6>Canned Responses</h6>
                </div>
                <div class="modal-body mt-0 mb-2">
                    <ul class="p-0 list-group list-group-flush">
                        <li class="response-li px-2"
                            v-for="response in responses"
                            :key="response.key"
                        ><a @click="getResponse(response.message)">{{ response.message }}</a> <i @click="pin(response.message)" class="icon-pin"></i></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['show'],
        data() {
            return {
                responses: [],
                res: ''
            }
        },
        mounted() {
            this.getResponses();
        },
        methods: {
            close() {
                this.$emit('close');
            },

            getResponses() {
                axios.get('/api/responses').then(res => {
                    this.responses = res.data.data;
                })
            },

            getResponse(text) {
                this.close();
                this.$emit('responsetext', text);
            },

            pin(message) {
                axios.post('/api/pins', {text: message}).then(res => {
                    this.$bus.$emit('PIN_ADDED');
                });

                const toast = this.$sweet.toast('top-end', false, 3000);
                toast.fire({
                    title: 'Success',
                    text: 'Pin was added',
                    type: 'success'
                })
            }
        }
    }
</script>

<style scoped>
    .modal-mask {
        position: absolute;
        z-index: 9998;
        top: -205px;
        left: 25px;
        width: 100%;
        height: 100%;
        transition: opacity .3s ease;
    }

    .modal-container {
        border-radius: 7px;
        width: 450px;
        padding: 10px 10px;
        background-color: #fff;
        box-shadow: 0 2px 13px rgba(0, 0, 0, .33);
        transition: all .3s ease;
        overflow-y: scroll;
        height: 300px;
    }

    .modal-container ul {
        list-style: none;
    }

    .modal-container ul li:hover {
        background: #98ca3f;
        color: white;
    }

    .modal-header h6 {
        margin-top: 0;
        color: #98CA41;
    }

    .modal-body {
        margin: 20px 0;
    }

    .modal-enter .modal-container,
    .modal-leave-active .modal-container {
        -webkit-transform: scale(1.1);
        transform: scale(1.1);
    }

    .icons-wrapper {
        position:relative;
    }
    .response-li a, .response-li i {
        cursor: pointer;
    }
</style>