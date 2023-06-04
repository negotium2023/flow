<template>
  <div class="create-card elevation-4" style="width: 800px;">
    <div class="card-header">
      <small style="color: #4ebf85;">Creating a card template for</small>
      <h5>{{section.name}}</h5>
    </div>
    <div class="card-body" style="direction: rtl">
      <div style="direction: ltr">
        <div class="row" style="margin-top:0px;direction: ltr;">
          <div class="col-md-12 pb-0 pt-0">
            <small>Case Summary</small>
            <input type="text" v-model="section.card_form.summary_description" class="form-control form-control-sm">
          </div>
        </div>
      <div class="row" style="margin-top: 1rem !important;">
        <div class="col-md-12 mb-0 mt-0 pt-0">
          <small class="card-lables">Card Name</small>
          <input type="text" class="form-control form-control-sm task-name" placeholder="Add card name" v-model="section.card_form.name" v-bind:class="{'is-invalid' : hasError}">
        </div>
      </div>
      <div class="row" style="margin-top: 0px !important;">
        <div class="col-md-12 mb-0 mt-0 pt-0">
          <small class="card-lables">Client</small>
          <multiselect @input="updateClient(section.card_form.client_name)" v-model="section.card_form.client_name" :custom-label="customLabel" placeholder="Select one" :options="office_clients" :searchable="true" :allow-empty="true" :multiple="false" :close-on-select="true">
          </multiselect>
        </div>
      </div>
      <!--<div class="row" style="margin-top: 0px !important;">
        <div class="col-md-4 mb-0 mt-0 pt-0">
          <small>Insurer</small>
          <input type="text" class="form-control form-control-sm" v-model="section.card_form.insurer" />
        </div>
        <div class="col-md-4 mb-0 mt-0 pt-0">
          <small class="card-lables">Policy</small>
          <input type="text" class="form-control form-control-sm" v-model="section.card_form.policy" />
        </div>
        <div class="col-md-4 mb-0 mt-0 pt-0">
          <small class="card-lables">Dependency</small>
          <select class="form-control form-control-sm" v-model="section.card_form.dependency_id">
            <option value="" disabled>Select Card</option>
            <option v-for="card in section.card_form.cards_drop_down" :value="card.id">{{card.name}}</option>
          </select>
        </div>
      </div>-->
      <div class="row" style="margin-top: 0px !important;">
        <div class="col-md-4 mb-0 mt-0 pt-0">
          <small class="card-lables">Sales representative on record</small>
          <select class="form-control form-control-sm" v-model="section.card_form.assignee_name" v-bind:class="{'is-invalid' : hasError}">
            <option value="" disabled>Select sales representative on record</option>
            <option v-for="user in office_advisors" :value="user">{{user}}</option>
          </select>
        </div>
        <div class="col-md-8 mb-0 mt-0 pt-0">
          <small class="card-lables">Assign Team members</small>
          <multiselect v-model="section.card_form.team_names" :custom-label="customLabel" placeholder="Select one" :options="office_users" :searchable="false" :allow-empty="true" :multiple="true" :close-on-select="true">
          </multiselect>
        </div>
      </div>
      <div class="row" style="margin-top: 0px !important;">
        <div class="col-md-6 mb-0 mt-0 pt-0">
          <small class="card-lables">Upfront Revenue</small>
          <input type="number" class="form-control form-control-sm" v-model="section.card_form.upfront_revenue" />
        </div>
        <div class="col-md-6 mb-0 mt-0 pt-0">
          <small class="card-lables">Ongoing Revenue</small>
          <input type="number" class="form-control form-control-sm" v-model="section.card_form.ongoing_revenue" />
        </div>
      </div>
      <div class="row" style="margin-top: 0px !important;">
        <div class="col-md-4 mb-0 mt-0 pt-0">
          <small class="card-lables">Due Date</small>
          <DueDate :open.sync="section.card_form.open2" @pick="handleChange" v-model="section.card_form.deadline" type="timestamp" v-bind:class="{'is-invalid' : hasError}"></DueDate>
        </div>
        <div class="col-md-4 mb-0 mt-0 pt-0">
          <small class="card-lables">Status</small>
          <select class="form-control form-control-sm" v-model="section.card_form.progress_status_id" v-bind:class="{'is-invalid' : hasError}">
            <option value="" disabled>Select Status</option>
            <option v-for="status in progress_status" :value="status.id">{{status.name}}</option>
          </select>
        </div>
        <div class="col-md-4 mb-0 mt-0 pt-0">
          <small class="card-lables">Priority</small>
          <select class="form-control form-control-sm" v-model="section.card_form.priority_status_id" v-bind:class="{'is-invalid' : hasError}">
            <option value="" disabled>Select Priority</option>
            <option v-for="status in priority_status" :value="status.id" v-bind:style="{color: status.fcolor}"><span>{{status.name}}</span></option>
          </select>
        </div>
      </div>
      <div class="row" style="margin-top:0px;border-bottom: 2px solid #b5c9d4;margin-bottom:1rem;">
        <div class="col-md-12 mb-0 mt-0 pt-0">
          <small class="card-lables">Description</small>
          <textarea rows="7" class="form-control form-control-sm"></textarea>
        </div>
      </div>
      <p><strong>Tasks</strong><a href="javascript:void(0)" class="text-primary float-right" @click="addTask"><i class="fa fa-plus"></i> Add task</a></p>
      <div class="col template-tasks">
        <div style="position:relative;border-bottom:1px solid #ecf1f4;padding-bottom:0.5rem;margin-bottom:0.5rem;" v-for="(taskItems, task_item_index) in tasks" :class="'task_'+task_item_index">
          <div class="row">
            <div class="col-lg-10 pt-0 pb-0">
              <input type="text"  v-bind:class="{'is-invalid' : hasError}" class="form-control form-control-sm task-name" placeholder="Add task name" v-model="taskItems.name">
            </div>
            <div class="col-lg-2 text-right pt-0 pb-0">
              <a href="#" @click="deleteTask(task_item_index)"><i class="fas fa-trash"></i></a>
            </div>
          </div>
          <span class="float-left">
          <small @click="assignTask(task_item_index)" v-if="taskItems.selected_assignee === ''"><i class="fas fa-user"></i> + Assign to a person</small>
          <small @click="assignTask(task_item_index)" v-if="taskItems.selected_assignee !== ''"><i class="fas fa-user"></i> {{taskItems.assignee_name}}</small>
          <div v-show="taskItems.assign_task" class="assign-person elevation-3">
            <div class="row pr-0 pb-0 pt-0 m-0">
              <div class="col-md-9 pl-0 pt-0 pb-0">
              <select v-model="taskItems.assignee_name" class="form-control form-control-sm" @change="taskUserAssigned(task_item_index)">
                <option disabled>Select User</option>
                <option v-for="user in office_users" :value="user">{{user}}</option>
              </select>
              </div>
            </div>
        </div>
        </span>
          <span class="float-right">
          <small @click="addDeadline(task_item_index)" v-if="taskItems.selected_duedate === ''"><span class="text-danger"><i class="fas fa-calendar-alt"></i></span> + Add deadline</small>
          <small @click="addDeadline(task_item_index)" v-if="taskItems.selected_duedate !== ''"><span class="text-danger"><i class="fas fa-calendar-alt"></i></span> {{taskItems.selected_duedate}}</small>
          <div class="assign-deadline elevation-3" v-show="taskItems.add_deadline">
            <DueDate v-model="taskItems.date" :open.sync="taskItems.open" @pick="handleChange2(task_item_index)" type="timestamp"></DueDate>
          </div>
          <small @click="addSubTasks(task_item_index)" class="ml-1">
            <span v-if="taskItems.subtasks && taskItems.subtasks.length === 1"><span class="text-info"><i class="fas fa-network-wired"></i></span> 0 subtasks</span>
            <span v-if="taskItems.subtasks && taskItems.subtasks.length > 1"><span class="text-info"><i class="fas fa-network-wired"></i></span> {{ taskItems.subtasks ? taskItems.subtasks.length - 1 : 0}} {{ taskItems.subtasks && taskItems.subtasks.length !== 1 ? "subtasks" : "subtask" }}</span>
          </small>
        </span>
          <div class="clearfix"></div>
          <div style="position:relative;display: block;" v-if="taskItems.add_sub_task">
            <div class="row subtask" v-for="(subtaskItems, subtask_item_index) in taskItems.subtasks" :class="'subtask_'+subtask_item_index">

              <div class="col-md-10 pt-0 pb-0">
                <input type="text" v-bind:class="{'is-invalid' : hasError}" class="form-control form-control-sm task-name" placeholder="Add subtask name" v-model="subtaskItems.name">
              </div>
              <div class="col-md-2 text-right pt-0 pb-0 pr-0">
                <a href="#" class="text-danger mr-1" @click="addSubtaskDeadline(task_item_index,subtask_item_index)"><i class="far fa-calendar-alt"></i></a>
                <div class="assign-deadline2 elevation-3" v-show="subtaskItems.add_deadline">
                  <DueDate v-model="subtaskItems.date" :open.sync="subtaskItems.open6" @pick="handleChange6(task_item_index,subtask_item_index)" type="timestamp"></DueDate>
                </div>
                <a href="#" @click="deleteSubtask(task_item_index,subtask_item_index)" class="delete-subtask"><i class="fas fa-trash"></i></a>
              </div>

            </div>
            <div class="addsubtask">
              <a href="javascript:void(0)" @click="addSubTask(task_item_index)">+ Add Subtask</a>
            </div>
            <!--<small><span class="text-info"><i class="fas fa-network-wired"></i></span></small>
            <input type="text" class="form-control form-control-sm task-name" placeholder="Add subtask name" v-model="taskItems.sub_task" v-on:keyup.enter="addSubTask" @blur="addSubTask">-->
          </div>
        </div>
      </div>
      </div>
    </div>
    <div class="card-footer clearfix">
      <button class="btn btn-sm btn-outline-info" @click="closeCreateCard">Cancel</button>
      <button type="button" @click="saveTask(section)" class="btn btn-sm btn-success float-right">Add template</button>
    </div>
  </div>

</template>

<script>
  import Multiselect from 'vue-multiselect'
  import DatePicker from 'vue2-datepicker';
  import 'vue2-datepicker/index.css';

  export default {
    name: "CreateCard.vue",
    props: ["section"],
    components: {
      "DueDate": DatePicker,
      "multiselect": Multiselect
    },
    watch: {
      'section': function (val, oldVal) {
        this.tasks = val.tasks;
        this.hasError = false;
      }
    },
    data: function () {
      return {
        hasError:false,
        tasks: [],
        card_form: {
          name: '',
          assignee_id: "",
          assignee_name: "",
          team_ids: [],
          team_names: [],
          due_date: '',
          ongoing_revenue: '',
          upfront_revenue: '',
          priority_status_id: "",
          progress_status_id: "",
          summary_description: '',
          description: '',
          cards_drop_down: []
        },
        card_name: '',
        sub_task: "",
        add_task: false,
        office_users: [],
        office_advisors: [],
        office_clients: [],
        assign_task: false,
        add_sub_task: false,
        deadline: "",
        add_deadline: false,
        team_ids: "",
        priority_status: [
          {id: '1', name: 'High Priority', fcolor: '#FF0000'},
          {id: '2', name: 'Medium Priority', fcolor: '#00FF00'},
          {id: '3', name: 'Low Priority', fcolor: '#0000FF'},
        ],
        progress_status: [
          {id: '1', name: 'Active'},
          {id: '2', name: 'InActive'},],
        card_tasks: [],
      }
    },
    mounted() {
      this.getOfficeUsers();
      this.getOfficeClients();
      this.getOfficeAdvisors();
      this.getCardStatus();
      this.getCardsDropDown();
    },
    methods: {
      getCardsDropDown(){
        axios.get('/card/get-cards')
                .then(response => {
                  /*console.log('card_drop_down', response.data.cards);*/
                  this.cards_drop_down = response.data.cards;
                  this.cards_drop_down.$forceUpdate();
                })
                .catch(error => {
                  console.log(error.response);
                });
      },
      taskUserAssigned(index){
        this.tasks[index].assign_task = false;
        this.tasks[index].selected_assignee = this.tasks[index].assignee_name;
      },
      handleChange() {
        this.section.card_form.open2 = false;
      },
      handleChange2(index) {
        const d = new Date(this.tasks[index].date);
        const t = d.getFullYear()  + "-" + (d.getMonth() + 1) + "-" + (d.getDate() + 0);

        this.tasks[index].assign_task = false;
        this.tasks[index].add_sub_task = false;
        this.tasks[index].selected_duedate = t;
        this.tasks[index].add_deadline = !this.tasks[index].add_deadline;
        this.tasks[index].open = false;
      },
      handleChange6(index,subindex) {
        const d = new Date(this.tasks[index].subtasks[subindex].date);
        const t = d.getFullYear()  + "-" + (d.getMonth() + 1) + "-" + (d.getDate() + 0);

        /*this.tasks[index].assign_task = false;
        this.tasks[index].add_sub_task = false;*/
        this.tasks[index].subtasks[subindex].selected_duedate = t;
        this.tasks[index].subtasks[subindex].add_deadline = !this.tasks[index].subtasks[subindex].add_deadline;

        this.tasks[index].subtasks[subindex].open6 = false;
      },
      customLabel (option) {
        return `${option}`
      },
      addTask() {
        let i = (this.tasks.length);

        if (!this.tasks) {
          //this.activities[index].rules = {rule_value:'',rule_process:'',rule_step:'',processs:'',stepss:''};
          []
        }

        this.tasks.push(
                {
                  name:"",
                  assignee_id:null,
                  subtasks:[{
                    name:"",
                    selected_duedate: "",
                    add_deadline: false,
                  }],
                  date:null,
                  assign_task:false,
                  add_deadline:false,
                  selected_assignee:'',
                  selected_duedate:'',
                  open: false,
                  add_sub_task: false
                }
        );
        /*this.add_task = true;*/
      },
      getOfficeUsers() {
        axios.get('/task')
                .then(response => {
                  this.office_users = response.data.office_users;
                })
                .catch(error => {
                  console.log(error.response);
                });
      },
      getOfficeAdvisors(){
        axios.get('/task/advisor')
                .then(response => {
                  this.office_advisors = response.data.office_users;
                  this.office_advisors.$forceUpdate();

                })
                .catch(error => {
                  console.log(error.response);
                });
      },
      getOfficeClients(){
        axios.get('/card/get-office-clients')
                .then(response => {
                  this.office_clients = response.data.office_clients;
                })
                .catch(error => {
                  /*console.log(error.response);*/
                });
      },
      getCardStatus(){
        axios.get('card/status')
                .then(response => {
                  this.priority_status = response.data.priority_status;
                  this.progress_status = response.data.progress_status;
                })
                .catch(error => {
                  console.log(error)
                });
      },
      deleteTask(index){
        if(confirm("Are you sure you want to delete this task?")) {
          toastr.success('<strong>Success!</strong> Task was deleted successfully.');

          toastr.options.timeOut = 1000;
          this.tasks.splice((index), 1);
        }
      },
      deleteSubtask(index,sub_index){
        if(confirm("Are you sure you want to delete this subtask?")) {
          toastr.success('<strong>Success!</strong> Subtask was deleted successfully.');

          toastr.options.timeOut = 1000;
          this.tasks[index].subtasks.splice((sub_index), 1);

          if (this.tasks[index].subtasks.length === 0) {
            this.tasks[index].add_sub_task = !this.tasks[index].add_sub_task;
          }
        }
      },
      assignTask(index) {
        this.tasks[index].add_deadline = false;
        this.tasks[index].add_sub_tasks = false;
        this.tasks[index].assign_task = !this.tasks[index].assign_task;
      },
      addSubTasks(index){
        this.tasks[index].assign_task = false;
        this.tasks[index].add_deadline = false;
        this.tasks[index].add_sub_task = !this.tasks[index].add_sub_task;
      },
      addSubTask(index){

        this.tasks[index].subtasks.push({
          name:"",
          open6:false,
          selected_duedate:'',
          add_deadline: false,
        });
      },
      addDeadline(index){

        this.tasks[index].assign_task = false;
        this.tasks[index].add_sub_task = false;
        this.tasks[index].add_deadline = !this.tasks[index].add_deadline;
      },
      subtaskDeadlineSelected(index,subindex){

        const t = new Date(this.tasks[index].subtasks[subindex].date).toISOString().slice(0, 10);

        /*this.tasks[index].assign_task = false;
        this.tasks[index].add_sub_task = false;*/
        this.tasks[index].subtasks[subindex].selected_duedate = t;
        this.tasks[index].subtasks[subindex].add_deadline = !this.tasks[index].subtasks[subindex].add_deadline;
      },
      addSubtaskDeadline(index,subindex){
        this.tasks[index].subtasks[subindex].add_deadline = !this.tasks[index].subtasks[subindex].add_deadline;
      },
      closeCreateCard() {
        this.$emit('close-me')
      },
      saveTask(section){
console.log(section);
        let err = 0;

        if (this.section.card_form.name && this.section.card_form.name.length === 0){
          err++;
        }

        if (this.section.card_form.assignee_name && this.section.card_form.assignee_name.length === 0){
          err++;
        }

        /*if (this.section.card_form.deadline && this.section.card_form.deadline.length === 0){
          err++;
        }*/

        if (this.section.card_form.progress_status_id.length === 0){
          err++;
        }

        if (this.section.card_form.priority_status_id.length === 0){
          err++;
        }

          if(this.tasks.length === 0) {

              for (var i = 0; i < this.tasks.length; i++) {

                  if(this.tasks[i].name === "Add task name" || this.tasks[i].name === ''){
                      err++;
                  }

                  for (var j = 0; j < this.tasks[i].subtasks.length; j++) {
                      if(this.tasks[i].subtasks[j].name === "Add subtask name" || this.tasks[i].subtasks[j].name === ""){
                          err++;
                      }

                      if(this.tasks[i].subtasks[j].name === null){
                          err++;
                      }
                  }

                  this.tasks[i]["section_id"] = section.section_id;
              }
          }

        this.section.card_form.open2 = false;

        if(err > 0){
          this.hasError = true;
          return false;
        } else {
          this.hasError = false;
        }
        axios.post('/card-template/save', {
          section: this.section,
          task: this.tasks,
          card_name: this.section.card_name,
          card_form: this.section.card_form,
        })
                .then(response => {
                  this.$emit('new-template', response.data.Card);
                  /*this.$emit('new-task', response.data.Card);*/
                  toastr.success('<strong>Success!</strong> Card template was successfully created.');

                  toastr.options.timeOut = 1000;
                  this.$emit('close-me')
                })
                .catch(function (error) {
                  console.log(error.response);
                });
      }
    },
  }
</script>

<style scoped>
  .create-card{
    position: absolute;
    right: 0;
    top: 4.35rem;
    z-index: 10;
    background: #FFFFFF;
    height: calc(100vh - calc(70px + 3rem));
  }
  .task-name{
    border: none;
    border-left: 3px solid #06496f;
    font-weight: 900;
    color: #06496f;
  }
  .task-name:focus{
    border: 1px solid #b5c9d4;
    box-shadow: unset !important;
  }
  .position-relative>small{
    cursor: pointer;
  }
  select{
    color: #06496f;
  }
  deadline{
    width: 100%;
  }

  .card-body{
    height:calc(100% - 9.5rem);
    overflow: auto;
    overflow-x:hidden;
  }

  .subtask{
    width:100%;
    padding-left:1.5rem;
    margin:0px !important;
  }
  .addsubtask{
    width:100%;
    text-align: right;
    font-size: 0.75rem;
  }

  .create-card .card-body{
    padding: 0 0.7rem 0.7rem 0.7rem !important;
  }

  .mx-datepicker{
    width: 100% !important;
  }
</style>