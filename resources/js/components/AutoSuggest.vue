<template>
    <div class="suggestion-mask" @click="close" v-show="show" v-if="results.length > 0">
        <div class="suggestion-container" @click.stop>
            <div class="modal-body mt-0">
                <ul class="p-0 list-group list-group-flush">
                    <li class="response-li px-2"
                        ref="scroll"
                        v-for="(result, index) in results"
                        :key="index"
                        :class="{ 'is-active': index === counter }"
                        @click="setResult(result.message)"
                    >{{ result.message }}</li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['show', 'results', 'counter'],
        data () {
            return {
                focus: null
            }
        },
        methods: {
            close() {
                this.$emit('close');
            },

            setResult(result) {
                this.$emit('resultSelected', result);
            }
        }
    }
</script>

<style scoped>
    .suggestion-mask {
        position: absolute;
        z-index: 9998;
        top: -302px;
        left: 0px;
        width: 100%;
        height: 100%;
        transition: opacity .3s ease;
    }

    .suggestion-container {
        border-radius: 7px;
        width: 450px;
        padding: 10px 10px;
        background-color: #fff;
        box-shadow: 0 2px 13px rgba(0, 0, 0, .33);
        transition: all .3s ease;
        min-height: 300px;
    }

    .suggestion-container li:hover {
        background: #98ca3f;
        color: white;
        cursor: pointer;
    }

    .suggestion-container ul {
        list-style: none;
    }

    .suggestion-container ul li {
        margin-bottom: 5px;
    }

    .is-active {
        background: #98ca3f;
        color: white;
    }
</style>