<template>
    <div>
        <div class="wrapper" v-if="dismissed">
            <div class="fa-details px-4 pt-3">
                <div v-if="loading" class="lds-ring"></div>

                <!-- Select Insuarances you work with -->

                <div  class="form-wrapper" v-if="intro && !loading">
                    <h3>Select from below</h3>
                    <div>
                        <div v-for="(insuarance, index) in insuarances">
                            <div class="form-group form-check" v-if="index !== 0">
                                <input type="checkbox" :value="index" class="form-check-input" :id="insuarance.id" v-model="selected_insuarances">
                                <label class="form-check-label" :for="insuarance.id">{{insuarance.name}}</label>
                            </div>
                        </div>
                        <a href="" class="btn btn-sm btn-primary xlr-btn float-right" @click.prevent="pushSelected">Next</a>
                    </div>
                </div>

                <!-- End Of Select Insuarances you work with -->

                <div v-if="!intro">
                    <div v-if="step_number <= total_count">
                        <div class="form-header text-center">
                            <div style="display: block"><h3>{{checked_insuarances[step_number].name}}</h3></div>
                            <div
                                    class="step" v-for="(insu, index) in checked_insuarances"
                                    :class="{
                                        'step-active': step_number == index
                                    }"
                            >{{index + 1}}</div>
                        </div>

                        <ul id="errors" v-if="display_errors">
                            <li v-for="error in errors">{{error}}</li>
                        </ul>

                        <div class="form-group form-inline mb-0" v-for="input in checked_insuarances[step_number].inputs">
                            <label class="label col-md-3 text-left" style="justify-content:left" :for="input.label.toLowerCase().replace(' ','_')">{{input.label}}</label>
                            <div class="col-md-9">
                            <input
                                    :type="input.input_type.toLowerCase().replace('app\\forminput','')"
                                    class="form-control w-100"
                                    :id="input.label.toLowerCase().replace(' ','_')"
                                    v-model="step_data[input.label.toLowerCase().replace(' ','_')]">
                            </div>
                        </div>

                        <a href="" class="btn btn-sm btn-primary xlr-btn float-right" @click.prevent="sendStep">{{button_text}}</a>
                    </div>
                </div>
            </div>
        </div>

        <Wizard v-if="display_wizard"></Wizard>


    </div>
</template>

<script>
    import Wizard from "../Wizard";

    export default {
        name: "FADetails",
        components: {
            Wizard
        },
        data: function(){
            return {
                loading: true,
                insuarances: [],
                selected_insuarances: [],
                checked_insuarances: [],
                intro: true,
                step_number: 0,
                total_count: 0,
                step_data:{},
                button_text: "Next",
                errors: [],
                display_errors: false,
                display_wizard: false,
                wizard: {},
                dismissed: false
            }
        },
        mounted(){
            axios.get('/fa-details/load')
                .then(response => {
                    if(response.data.is_display){
                        this.dismissed = true;
                        this.insuarances = response.data.forms;
                        this.checked_insuarances.push(this.insuarances[0]);
                        this.loading = false;
                    }

                })
                .catch(function (error) {
                    // handle error
                    console.log(error);
                })
        },
        methods: {
            pushSelected: function () {
                this.selected_insuarances.forEach((item, index) =>{
                    this.checked_insuarances.push(this.insuarances[item])
                });

                this.intro = false;
                this.total_count = this.checked_insuarances.length - 1;
            },
            sendStep: function () {
                this.errors.length = [];
                this.checked_insuarances[this.step_number].inputs.forEach((item, index) => {

                    if (!this.step_data[item.label.toLowerCase().replace(' ','_')] && item.label != "Work Number"){
                        this.errors.push(item.label + " is required.");
                    }else if(item.label === "Email"){

                        if(!this.step_data[item.label.toLowerCase().replace(' ','_')]){
                            this.errors.push("Enter a valid email address");
                        }else{
                            const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                            re.test(String(this.step_data[item.label.toLowerCase().replace(' ','_')]).toLowerCase());
                            if(!re.test(String(this.step_data[item.label.toLowerCase().replace(' ','_')]).toLowerCase())){
                                this.errors.push("Please enter a valid email address");
                            }
                        }
                    }


                });

                if(this.errors.length != 0){
                    this.display_errors = true;
                    console.log("Error!")
                    return false;
                }


                let data = this.checked_insuarances[this.step_number].inputs.map((item) =>{
                    return {
                        id: item.id,
                        data: this.step_data[item.label.toLowerCase().replace(' ','_')],
                        input_type: item.input_type
                    }
                });

                axios.post('/fa-details', {
                    data: data,
                })
                    .then(response => {
                        if(this.step_number < this.total_count){
                            this.step_number = this.step_number + 1;
                        }else{
                            this.display_wizard = response.data.wizard_status === 1 ? false : true;
                            this.dismissed = false;
                        }
                        this.display_errors = false;
                        this.button_text = (this.step_number === this.total_count) ?"Save":"Next";
                    })
                    .catch(function (error) {
                      alert('error')
                        console.log(error.response.data);
                    });

            }
        }
    }
</script>

<style scoped>
    .wrapper{
        background-color: rgba(249, 249, 249,.92);
        position: absolute;
        width: 100%;
        height: 100vh;
        top: 0;
        left: 0;
        z-index: 9999!important;
    }
    .fa-details{
        width: 600px;
        margin: 0 auto;
        background: #ffffff;
        height: 100%;
        box-shadow: 2px 2px 15px #ccc;
    }
    .form-header{
        text-align: center;
        margin-bottom: 1rem;
    }
    .step{
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: inline-block;
        border: 1px solid #4c5e77;
        text-align: center;
        font-size: 1.5rem;
        margin-right: 20px;
    }
    .step-active{
        background: #4c5e77;
        color: #ffffff;
    }
    .step:last-child{
        margin-right: 0;
    }
    #errors{
        list-style: none;
    }
    #errors > li{
        border-left: 2px red solid;
        color: red;
        background: rgba(255, 0,0,.1);
        padding-left: 15px;
        padding-right: 15px;
        margin-bottom: 3px;
        margin-right: 3px;
        display: inline-block;
    }
    .lds-ring {
        display: flex;
        position: relative;
        width: 100%;
        height:100%;
        justify-content: center;
    }
    .lds-ring div {
        box-sizing: border-box;
        display: block;
        position: absolute;
        align-self: center;
        width: 64px;
        height: 64px;
        margin: 8px;
        border: 8px solid #4c5e77;
        border-radius: 50%;
        animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
        border-color: #4c5e77 transparent transparent transparent;
    }
    .lds-ring div:nth-child(1) {
        animation-delay: -0.45s;
    }
    .lds-ring div:nth-child(2) {
        animation-delay: -0.3s;
    }
    .lds-ring div:nth-child(3) {
        animation-delay: -0.15s;
    }
    @keyframes lds-ring {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

</style>