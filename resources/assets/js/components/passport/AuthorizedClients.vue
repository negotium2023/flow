<style scoped>
    .action-link {
        cursor: pointer;
    }

    .m-b-none {
        margin-bottom: 0;
    }
</style>

<template>
    <div>
        <div>
            <div class="panel panel-default">

                <div class="table-responsive">
                    <table class="table table-borderless  table-sm">
                        <tbody>
                        <tr>
                            <td><h3>Authorized Applications</h3></td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="panel-body">

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-sm">
                            <thead class="btn-dark">
                                <tr>
                                    <th>Name</th>
                                    <th>Scopes</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="token in tokens">
                                    <!-- Client Name -->
                                    <td style="vertical-align: middle;">
                                        {{ token.client.name }}
                                    </td>

                                    <!-- Scopes -->
                                    <td style="vertical-align: middle;">
                                        <span v-if="token.scopes.length > 0">
                                            {{ token.scopes.join(', ') }}
                                        </span>
                                    </td>

                                    <!-- Edit Button -->
                                    <td style="vertical-align: middle;" class="text-right">
                                        <a class="btn btn-sm btn-danger ml-2 mr-2" @click="revoke(token)">
                                            <i class="fa fa-trash"></i> Revoke
                                        </a>
                                    </td>
                                </tr>
                                <!-- Current Authorized Tokens -->
                                <tr v-if="tokens.length === 0">
                                    <td colspan="3">You have no authorized tokens.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        /*
         * The component's data.
         */
        data() {
            return {
                tokens: []
            };
        },

        /**
         * Prepare the component (Vue 1.x).
         */
        ready() {
            this.prepareComponent();
        },

        /**
         * Prepare the component (Vue 2.x).
         */
        mounted() {
            this.prepareComponent();
        },

        methods: {
            /**
             * Prepare the component (Vue 2.x).
             */
            prepareComponent() {
                this.getTokens();
            },

            /**
             * Get all of the authorized tokens for the user.
             */
            getTokens() {
                axios.get('/oauth/tokens')
                .then(response => {
                    this.tokens = response.data;
                });
            },

            /**
             * Revoke the given token.
             */
            revoke(token) {
                let confirmFlag = confirm('Ayre you sure you want to delete this client?');

                if(!confirmFlag){
                    return false;
                }

                axios.delete('/oauth/tokens/' + token.id)
                .then(response => {
                    this.getTokens();
                });
            }
        }
    }
</script>
