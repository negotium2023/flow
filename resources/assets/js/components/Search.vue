<template>
    <form title="Search contents of the application" class="form-inline mr-3">
        <div class="input-group input-group-sm">
            <div class="input-group-prepend" style="margin-top:0px;">
                <span class="input-group-text"><i class="fa fa-search"></i></span>
            </div>
            <input class="form-control form-control-sm" type="text" placeholder="Search..." data-toggle="dropdown" style="margin-top:0px;" v-model="query" @input="getResults">
            <div class="dropdown-menu dropdown-menu-left blackboard-search-dropdown" v-show="results.length>0 || isLoading">
                <a :href="result.route" class="dropdown-item blackboard-search-result" v-for="result in results">
                    <span class="blackboard-search-result-name">{{result.name}}</span>
                    <span class="blackboard-search-result-type float-right">{{result.type}}</span>
                </a>
                <a href="#" class="dropdown-item text-center" v-if="isLoading"><i class="fa fa-circle-o-notch fa-spin fa-fw"></i> Loading</a>
            </div>
        </div>
    </form>
</template>

<script>
    export default {
        data() {
            return {
                query: '',
                results: [],
                isLoading: false
            }
        },
        methods: {
            getResults: _.debounce(
                function () {
                    this.isLoading = true;
                    this.results = [];
                    axios.get('/search?q=' + this.query)
                        .then(response => {
                            this.isLoading = false;
                            this.results = response.data;

                        })
                        .catch(error => {
                            this.isLoading = false;
                        });
                },
                500
            )
        }
    }
</script>
