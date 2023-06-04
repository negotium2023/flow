<template>
    <div>
        <div class="btn btn-sm btn-primary" data-toggle="modal" data-target="#exampleModal">Edit FA Details</div>
        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{results[sectionsIndex].name}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group" v-for="input in results[sectionsIndex].inputs">
                            <label :for="input.label.toLowerCase().replace(' ','_')">{{input.label }}</label>
                            <input
                                    :type="input.input_type.toLowerCase().replace('app\\forminput','')"
                                    class="form-control"
                                    :id="input.label.toLowerCase().replace(' ','_')"
                                    v-model="section[input.label.toLowerCase().replace(' ','_')]">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary xlr-btn float-right" @click.prevent="nextStep">{{buttonText}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data: () => {
            return {
                sectionsIndex: 0,
                sectionTitle:'',
                results: [{name:""}],
                section: {},
                buttonText: "Next"
            }
        },
        mounted() {
            axios.get('/fa-details/load?edit=true')
            .then(response =>{
                let loc = response.data.forms.map(item => {
                    let local;
                    if(item.inputs.length > 0){
                        return item;
                    }
                })

                this.results = loc.filter(item => item !== undefined);



                if(response.data.forms[this.sectionsIndex].inputs.length > 0){
                    response.data.forms[this.sectionsIndex].inputs.forEach((item, index) =>{
                        this.section[item.label.toLowerCase().replace(' ','_')] = item.data;
                    });
                }
            })
            .catch(response =>{
                console.log(response)
            })
        },
        methods:{
            nextStep(){
                let data = [];
                this.results[this.sectionsIndex].inputs.forEach((input, index) =>{
                    data.push({
                        id: input.data_id,
                        input_type_id: input.id,
                        data: this.section[input.label.toLowerCase().replace(' ','_')],
                        input_type: input.input_type,
                    })
                })

                axios.post('/fa-details', {
                    data: data,
                    edit: true,
                })
                    .then(response => {
                        if (this.sectionsIndex === (this.results.length - 1)){
                          $("#exampleModal").modal("hide");
                        }
                        if (this.sectionsIndex <= (this.results.length - 1)){
                            this.sectionsIndex = this.sectionsIndex + 1;
                            this.results[this.sectionsIndex].inputs.forEach((item, index) =>{
                                this.section[item.label.toLowerCase().replace(' ','_')] = item.data;
                            });
                        }

                        if (this.sectionsIndex === (this.results.length - 1)){
                          this.buttonText = "Save";
                        }

                    })
                    .catch(function (error) {
                        if (error.response !== undefined) alert("Something went wrong")
                        //console.log(error);
                    });
            }
        }
    }
</script>

<style scoped>

</style>