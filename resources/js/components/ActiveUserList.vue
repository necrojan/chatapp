<template>
    <div class="active-users">
        <div class="row">
            <div class="col-md-12">
                <h4 class="mb-3">Active Employees</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group mb-3 has-search">
                    <span class="fa fa-search form-control-feedback"></span>
                    <input v-model="search" type="text" class="form-control" placeholder="Search by Name">
                </div>
            </div>
        </div>
        <b-table
                :items="userEmployees"
                :fields="fields"
                :head-variant="'dark'"
                :per-page="perPage"
                :current-page="currentPage"
                id="logged-users"
        >
            <template v-slot:cell(photo)="data">
                <img :src="getProfileImage(data.item.photo)">
            </template>
        </b-table>
        <b-pagination
                v-model="currentPage"
                :total-rows="rows"
                :per-page="perPage"
                aria-controls="logged-users"></b-pagination>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                activeUsers: [],
                search: '',
                fields: [
                    {
                        key: 'name',
                        label: 'Full Name'
                    },
                    'email',
                    {
                        key: 'photo',
                        label: 'Photo'
                    },
                    {
                        key: 'username',
                        label: 'User Name'
                    }
                ],
                currentPage: 1,
                perPage: 10
            }
        },

        computed: {
            userEmployees() {
                return this.activeUsers.filter((user) => {
                    return !user.user;
                }).filter((user) => {
                    return user.name.toLowerCase().includes(this.search.toLowerCase());
                })
            },

            rows() {
                return this.userEmployees.length;
            }
        },

        created() {
            Echo.join('restore.pool')
                .here((users) => {
                    this.activeUsers = users;
                })
                .joining((user) => {
                    this.activeUsers.push(user);
                })
                .leaving((user) => {
                    this.activeUsers.splice(this.activeUsers.indexOf(user), 1);
                })
            .listen('RestorePool', e => {
                this.$cookie.delete(e.client.user.id + 'accepted-by');
            });

            Echo.channel('accepted.by')
                .listen('AcceptedBy', e => {
                    const user = this.activeUsers.find((activeUser) => {
                        if (activeUser.user && e.client.user) {
                            return activeUser.user.id === e.client.user.id
                        }
                    });

                    if (user) {
                        this.$cookie.set(user.user.id + 'accepted-by', e.user.name);
                    } else {
                        if (e.client.user) {
                            this.$cookie.set(e.client.user.id + 'accepted-by', e.user.name);
                        }
                    }
                });
        },

        methods: {
            getProfileImage(name) {
                const path = 'storage/images';
                if (!name) {
                    return path + '/no-image.png';
                }
                return path + `/${name}`
            }
        }
    }
</script>

<style scoped>
    .active-users img {
        width: 40px;
        height: 40px;
    }
</style>