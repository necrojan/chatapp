<template>
    <div class="pin-mask" @click="close" v-show="show">
        <div class="pin-container" @click.stop>
            <div class="pin-header modal-header">
                <h6>Pinned Responses</h6>
            </div>
            <div class="modal-body mt-0">
                <ul class="p-0 list-group list-group-flush">
                    <li class="response-li"
                        v-for="message in messages"
                        :key="message.id"
                    ><a @click="sendResponse(message.text)">{{ message.text }}</a> <i class="icon-close" @click="unPin(message)"></i></li>
                </ul>
                <p v-if="pins.length === 0">No Pinned Responses</p>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['show'],
        data () {
            return {
                pins: []
            }
        },

        mounted() {
            this.getPins();
            this.$bus.$on(['PIN_ADDED', 'PIN_DELETED'], () => {
                this.getPins();
            });
        },

        computed: {
            messages() {
                return this.pins.reduce((acc, curEl) => {
                    const x = acc.find(item => item.text === curEl.text);
                    if (!x) {
                        return acc.concat([curEl]);
                    } else {
                        return acc;
                    }
                }, []);
            }
        },

        methods: {
            close() {
                this.$emit('close');
            },

            getPins() {
                axios.get('/api/pins').then((res) => {
                    this.pins = res.data;
                });
            },

            unPin(message) {
                axios.delete(`/api/pins/${message.id}`).then(() => {
                    this.$bus.$emit('PIN_DELETED');
                });

                const toast = this.$sweet.toast('top-end', false, 3000);
                toast.fire({
                    title: 'Removed!',
                    text: 'Pin was removed',
                    type: 'success'
                });
            },

            sendResponse(text) {
                this.close();
                this.$emit('pinmessage', text);
            }
        }
    }
</script>

<style scoped>
    .pin-mask {
        position: absolute;
        z-index: 9998;
        top: -205px;
        left: 53px;
        width: 100%;
        height: 100%;
        transition: opacity .3s ease;
    }

    .pin-container {
        border-radius: 7px;
        width: 450px;
        padding: 10px 10px;
        background-color: #fff;
        box-shadow: 0 2px 13px rgba(0, 0, 0, .33);
        transition: all .3s ease;
        overflow-y: scroll;
        height: 300px;
    }

    .pin-container ul {
        list-style: none;
    }

    .pin-container ul li a, .pin-container ul li i {
        cursor: pointer;
    }

    .modal-header h6 {
        margin-top: 0;
        color: #98CA41;
    }
</style>