<template>
    <div>
        <div class="row">
            <div class="col-md-4">
                <div class="search-text-container">
                    <h4 class="mb-3">Archived Messages</h4>
                    <div class="search-wrapper">
                        <div class="form-group mb-3 has-search">
                            <span class="fa fa-search form-control-feedback"></span>
                            <input v-model="keyword" type="text" class="form-control" placeholder="Search by Message">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{ userMessages }}
        <b-table
            :items="userMessages"
            :fields="fields"
            :head-variant="'dark'"
            :per-page="perPage"
            :current-page="currentPage"
            id="user-messages"
        >
            <template v-slot:cell(created_at)="data">
                {{ $formatter.dateFormat(data.item.created_at, 'MMM Do YY') }}
            </template>
        </b-table>

        <b-pagination
                v-model="currentPage"
                :total-rows="rows"
                :per-page="perPage"
                aria-controls="user-messages">
        </b-pagination>
    </div>
</template>

<script>
    export default {
        data () {
            return {
                results: [],
                keyword: '',
                fields: [
                    {
                        key: 'created_at',
                        label: 'Date'
                    },
                    'message'
                ],
                currentPage: 1,
                perPage: 3
            }
        },

        mounted () {
            this.getUserMessages();
        },

        computed: {
            userMessages() {
                return this.results.filter((user) => {
                    return user.messages.length > 0;
                }).filter((user) => {
                    console.log(user)
                });
            },

            rows() {
                return this.results.length;
            }
        },

        methods: {
            getUserMessages() {
                axios.get('/archived-messages').then(response => {
                    this.results = response.data;
                })
            }
        }
    }
</script>

<style scoped>

</style>