<template>
    <div class="personal-modal-mask" @click="close" v-show="show" >
        <div class="personal-modal-container" @click.stop>
            <div class="modal-header text-center">
                <h6>Personal Canned Responses</h6>
            </div>
            <div class="modal-body mt-0 mb-2">
                <ul class="p-0 list-group list-group-flush">
                    <li class="response-li px-2"
                        v-for="response in responses"
                        :key="response.key"
                    ><a @click="getResponse(response.message)">{{ response.message }}</a></li>
                </ul>
                <p v-if="responses.length === 0">No Personal Responses</p>
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
                axios.get('/api/personal-responses').then(res => {
                    this.responses = res.data;
                })
            },

            getResponse(text) {
                this.close();
                this.$emit('personalresponsetext', text);
            }
        }
    }
</script>

<style scoped>
    .personal-modal-mask {
        position: absolute;
        z-index: 9998;
        top: -205px;
        left: 75px;
        width: 100%;
        height: 100%;
        transition: opacity .3s ease;
    }

    .personal-modal-container {
        border-radius: 7px;
        width: 450px;
        padding: 10px 10px;
        background-color: #fff;
        box-shadow: 0 2px 13px rgba(0, 0, 0, .33);
        transition: all .3s ease;
        overflow-y: scroll;
        height: 300px;
    }

    .personal-modal-container ul {
        list-style: none;
    }

    .personal-modal-container ul li:hover {
        background: #98ca3f;
        color: white;
    }

    .response-li a {
        cursor: pointer;
    }

    .modal-header h6 {
        margin-top: 0;
        color: #98CA41;
    }

    .modal-body {
        margin: 20px 0;
    }
</style>