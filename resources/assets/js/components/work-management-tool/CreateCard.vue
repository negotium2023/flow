  <template>
  <div class="create-card elevation-4" style="width: 800px;">
    <div class="card-header">
      <small style="color: #06496f;opacity: 0.5;">{{section.name}}</small>
      <div class="row">
        <div class="col-md-12 m-0 pt-0 pb-0 pr-0">
        <input type="text" class="form-control form-control-sm" v-bind:class="{'is-invalid' : hasError}" v-model="section.card_form.name" @input="autosaveCard(section);">
        </div>
        <!--<div class="col-md-2 m-0 text-right">
          <a href="javascript:void(0)"  @click="linkClient()"><i class="fas fa-link"></i></a>
        </div>-->
      </div>

    </div>
    <div class="card-body" style="direction: rtl">
      <div style="direction: ltr">
      <div class="row" style="margin-top:0px;direction: ltr;">
        <div class="col-md-12 pb-0 pt-0">
          <small>Case Summary</small>
          <input type="text" v-model="section.card_form.summary_description" class="form-control form-control-sm">
        </div>
      </div>
      <div class="row" style="margin-top:1rem;">
          <input type="hidden" class="form-control form-control-sm" v-model="section.card_form.saved" />
        <div class="col-md-12 mb-0 mt-0 pt-0">
          <small class="card-lables">Client</small>
          <multiselect v-bind:class="{'is-invalid' : (section.card_form.client_name !== '' && section.card_form.client_name !== null && hasError)}" @input="updateClient(section.card_form.client_name);autosaveCard(section);" v-model="section.card_form.client_name" :custom-label="customLabel" placeholder="Select one" :options="office_clients" :searchable="true" :allow-empty="true" :multiple="false" :close-on-select="true">
          </multiselect>
        </div>
      </div>
      <!--<div class="row" style="margin-top:0px;">
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
            <option v-for="card in cards_drop_down" :value="card.id">{{card.name}}</option>
          </select>
        </div>
      </div>-->
      <div class="row" style="margin-top:0px;">
        <div class="col-md-4 mb-0 mt-0 pt-0">
          <small class="card-lables">Sales representative on record</small>
          <select  class="form-control form-control-sm" v-bind:class="{'is-invalid' : hasError}" v-model="section.card_form.assignee_name" @input="autosaveCard(section);">
            <option value="" disabled>Select sales representative on record</option>
            <option v-for="user in office_advisors" :value="user">{{user}}</option>
          </select>
        </div>
        <div class="col-md-8 mb-0 mt-0 pt-0">
          <small class="card-lables">Assign Team members</small>
          <multiselect v-model="section.card_form.team_names" :custom-label="customLabel" placeholder="Select one" :options="office_users" :searchable="false" :allow-empty="true" :multiple="true" :close-on-select="true" @input="autosaveCard(section);">
          </multiselect>
        </div>
      </div>
      <div class="row" style="margin-top:0px;">
        <div class="col-md-6 mb-0 mt-0 pt-0">
          <small class="card-lables">Upfront Revenue</small>
          <input type="number" class="form-control form-control-sm" v-model="section.card_form.upfront_revenue" />
        </div>
        <div class="col-md-6 mb-0 mt-0 pt-0">
          <small class="card-lables">Ongoing Revenue</small>
          <input type="number" class="form-control form-control-sm" v-model="section.card_form.ongoing_revenue" />
        </div>
      </div>
      <div class="row" style="margin-top:0px;">
        <div class="col-md-4 mb-0 mt-0 pt-0">
          <small class="card-lables">Due Date</small>
          <DueDate v-bind:class="{'is-invalid' : hasError}" :open.sync="section.card_form.open" @pick="handleChange" v-model="section.card_form.deadline" :placeholder="section.card_form.selected_deadline" type="date"></DueDate>
        </div>
        <div class="col-md-4 mb-0 mt-0 pt-0">
          <small class="card-lables">Status</small>
          <select class="form-control form-control-sm" v-bind:class="{'is-invalid' : hasError}" v-model="section.card_form.progress_status_id" @change="autosaveCard(section);">
            <option value="" disabled>Select Status</option>
            <option v-for="status in progress_status" :value="status.id">{{status.name}}</option>
          </select>
        </div>
        <div class="col-md-4 mb-0 mt-0 pt-0">
          <small class="card-lables">Priority</small>
          <select class="form-control form-control-sm" v-bind:class="{'is-invalid' : hasError}" v-model="section.card_form.priority_status_id" @change="autosaveCard(section);">
            <option value="" disabled>Select Priority</option>
            <option v-for="status in priority_status" :value="status.id" v-bind:style="{color: status.fcolor}"><span>{{status.name}}</span></option>
          </select>
        </div>
      </div>
      <div class="row" style="margin-top:0px;border-bottom: 2px solid #b5c9d4;margin-bottom:1rem;">
        <div class="col-md-12 mb-0 mt-0 pt-0">
          <small class="card-lables">Description</small>
          <textarea rows="7" v-model="section.card_form.description" class="form-control form-control-sm"></textarea>
        </div>
      </div>
      <p><strong>Tasks</strong><a href="javascript:void(0)" class="text-primary float-right" @click="addTask"><i class="fa fa-plus"></i> Add task</a></p>
      <div class="col template-tasks">
        <div style="position:relative;border-bottom:1px solid #ecf1f4;padding-bottom:0.5rem;margin-bottom:0.5rem;" v-for="(taskItems, task_item_index) in tasks" :class="'task_'+task_item_index">
        <div class="row">
        <div class="col-md-10 pt-0 pb-0">
          <input type="text" v-bind:class="{'is-invalid' : hasError}" class="form-control form-control-sm task-name" placeholder="Add task name" v-model="taskItems.name">
        </div>
        <div class="col-md-2 text-right pt-0 pb-0">
          <a href="#" @click="deleteTask(task_item_index)"><i class="fas fa-trash"></i></a>
        </div>
      </div>
        <span class="float-left">
          <small @click="assignTask(task_item_index)" v-if="taskItems.selected_assignee === '' || taskItems.assignee_name === ''"><i class="fas fa-user"></i> + Assign to a person</small>
          <small @click="assignTask(task_item_index)" v-if="taskItems.selected_assignee !== '' && taskItems.assignee_name !== ''"><i class="fas fa-user"></i>  {{taskItems.selected_assignee !== '' && taskItems.selected_assignee !== null ? taskItems.selected_assignee : taskItems.assignee_name}}</small>
          <div v-show="taskItems.assign_task" class="assign-person elevation-3">
            <div class="row pr-0 pb-0 pt-0 m-0">
              <div class="col-md-9 pl-0 pt-0 pb-0">
              <select v-model="taskItems.assignee" class="form-control form-control-sm" @change="taskUserAssigned(task_item_index)">
                <option disabled>Select User</option>
                <option v-for="user in office_users" :value="user">{{user}}</option>
              </select>
              </div>
            </div>
        </div>
        </span>
        <span class="float-right">
          <small @click="addDeadline(task_item_index)" v-if="taskItems.selected_duedate === ''"><span class="text-danger"><i class="fas fa-calendar-alt"></i></span> + Add deadline</small>
          <small @click="addDeadline(task_item_index)" v-if="taskItems.selected_duedate !== ''"><span class="text-danger"><i class="fas fa-calendar-alt"></i></span> {{taskItems.selected_duedate !== '' && taskItems.selected_duedate !== null ? taskItems.selected_duedate : taskItems.due_date}}</small>
          <div class="assign-deadline elevation-3" v-show="taskItems.add_deadline">
            <DueDate v-model="taskItems.date" :open.sync="taskItems.open6" @pick="handleChange2(task_item_index);autosaveCard(section);" type="date"></DueDate>
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
                  <DueDate v-model="subtaskItems.date" :open.sync="subtaskItems.open14" @pick="handleChange3(task_item_index,subtask_item_index);autosaveCard(section);" type="timestamp"></DueDate>
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
      <div class="card-discussion">
        <p><strong>Discussion</strong></p>
        <div class="row">
          <div class="col-md-10 pt-0 pb-0 pr-0 m-0">
            <input type="text" v-model="discussion_message" class="form-control form-control-sm">
          </div>
          <button type="button" @click="saveDiscussionMessage()" class="btn btn-sm btn-primary float-right ml-1">Post</button>
        </div>
        <div v-for="discussion in discussions">
          <strong>{{ (discussion.user.first_name?discussion.user.first_name:"") + " " + (discussion.user.last_name?discussion.user.last_name:"")}}</strong>
          <p>{{discussion.message}}</p>
          <hr>
        </div>
      </div>
      <div class="card-recordings">
        <p><strong>Voice Notes</strong></p>
        <div>
          <vue-record-audio @result="onResult" style="height:40px !important;width:40px !important;" />
          <audio id="audioPlayer" controls src="#" type="audio/mpeg" style="height:40px !important;width:calc(100% - 140px);"></audio>
          <a href="#" type="button" class="btn btn-secondary" style="margin-left: 7px;position:relative;top:-16px;line-height:1.25rem;margin-bottom:0px;" @click="saveVoice()" >Save</a>
        </div>
        <hr>
        <div v-for="voicenote in recordings">
          <div>
            <audio class="audioPlayer" controls :src="'/storage/recording/' + voicenote.recording" type="audio/mpeg" style="width:100%;height:40px;"></audio>
            <small class="text-muted float-left" style="margin-left: 10px;position:relative;font-size:65%;padding-bottom:7px;"><i class="fas fa-user"></i> {{ voicenote.user }}</small>
            <small class="text-muted float-right" style="margin-left: 10px;position:relative;font-size:65%;padding-bottom:7px;"><i class="fas fa-clock"></i> {{ voicenote.created_at }}</small>
          </div>
          <hr>
        </div>
      </div>
      </div>
    </div>
    <div class="card-footer clearfix">
      <button class="btn btn-sm btn-outline-info" @click="closeCreateCard">Cancel</button>
      <button type="button" @click="saveTask(section)" class="btn btn-sm btn-success float-right">Save Case</button>
    </div>

    <!--<div class="modal-mask" v-show="link_client" transition="modal" aria-hidden="true" role="dialog" style="position: fixed;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -50%);">
      <div class="modal-dialog">
        <div class="modal-content" style="width:800px;">
          <div class="modal-header">

            <span class="modal-title">Link Client</span>
            <button type="button" class="close" @click="closeLinkClient()">&times;</button>
          </div>
          <div class="modal-body link-client-modal" style="overflow:inherit;">
            <multiselect v-model="section.card_form.client_name" :custom-label="customLabel" placeholder="Select one" :options="office_clients" :searchable="true" :allow-empty="true" :multiple="false" :close-on-select="true">
            </multiselect>
            <button class="btn btn-sm btn-default float-right ml-1" @click="closeLinkClient()">Cancel</button>
            <button class="btn btn-sm btn-primary float-right" @click="saveLinkClient()">Save</button>
          </div>
        </div>
      </div>
    </div>-->
  </div>

</template>

<script>
import Multiselect from 'vue-multiselect'
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';
import VueRecord from '@codekraft-studio/vue-record';
import AudioRecorder from 'vue-audio-recorder'

export default {
  name: "CreateCard.vue",
  props: ["section"],
  components:{
    "DueDate": DatePicker,
    "multiselect": Multiselect,
    'VueRecord' : VueRecord,
    'AudioRecorder' : AudioRecorder,
  },
  watch: {
    'section': function (val, oldVal) {
      this.tasks = val.tasks;
      this.hasError = false;
      this.card_form.open2 = false;
      this.card_form.saved = val.saved;
      this.card_form.selected_deadline = val.selected_deadline;
      this.recordings = [];
      this.discussions = [];
      this.discussion_message = '';
    }
  },
  data: function (){
    return {
      hasError: false,
      tasks:[],
      card_form: {
        saved: 0,
        name: null,
        insurer: '',
        policy: '',
        dependency_id: '',
        assignee_id: "",
        assignee_name: "",
        team_ids: [],
        team_names: "",
        client_name: "",
        deadline: '',
        selected_deadline: '',
        priority_status_id: "",
        progress_status_id: "",
        description: '',
        updateFlag: 0,
        updateClient: 0,
        ongoing_revenue: '',
        upfront_revenue: '',
        open:false,
        open2:false
      },
      card_name: '',
      sub_task: "",
      link_client: false,
      closedLinkClient: false,
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
      cards_drop_down:[],
      discussions: [],
      discussion_message: '',
      recordedAudio: null,
      recordings: [],
    }
  },
  mounted() {
    this.getOfficeUsers();
    this.getOfficeClients();
    this.getOfficeAdvisors();
    this.getCardStatus();
    this.tasks = [];
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
                /*console.log(error.response);*/
              });
    },
    saveLinkClient(){
      this.link_client = false;
    },
    closeLinkClient(){
      this.closedLinkClient = true;
      this.link_client = false;
    },
    linkClient(){
      this.link_client = true;
    },
    taskUserAssigned(index){
      this.tasks[index].selected_assignee = this.tasks[index].assignee;
      this.tasks[index].assignee_name = this.tasks[index].assignee;
      this.tasks[index].assign_task = !this.tasks[index].assign_task;
    },
    handleChange(value, type) {
      const d = new Date(this.section.card_form.deadline);
      const t = d.getFullYear()  + "-" + (d.getMonth() + 1) + "-" + (d.getDate() + 0);

      this.section.card_form.selected_deadline = t;

      this.section.card_form.open = false;
      this.autosaveCard(this.section);
    },
    handleChange2(index) {
      const d = new Date(this.tasks[index].date);
      const t = d.getFullYear()  + "-" + (d.getMonth() + 1) + "-" + (d.getDate() + 0);

      this.tasks[index].assign_task = false;
      this.tasks[index].add_sub_task = false;
      this.tasks[index].selected_duedate = t;

      this.tasks[index].add_deadline = false;
      this.tasks[index].open6 = false;

      this.autosaveCard(this.section);
    },
    handleChange3(index,subindex) {
      const d = new Date(this.tasks[index].subtasks[subindex].date);
      const t = d.getFullYear()  + "-" + (d.getMonth() + 1) + "-" + (d.getDate() + 0);

      /*this.tasks[index].assign_task = false;
      this.tasks[index].add_sub_task = false;*/
      this.tasks[index].subtasks[subindex].selected_duedate = t;
      this.tasks[index].subtasks[subindex].add_deadline = false;

      this.tasks[index].subtasks[subindex].open14 = false;
      this.autosaveCard(this.section);
    },
    customLabel (option) {
      return `${option}`
    },
    addTask(){
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
                open6: false,
                add_sub_task: false
              }
      );
      /*this.add_task = true;*/
    },
    getOfficeUsers(){
      axios.get('/task')
              .then(response => {
                this.office_users = response.data.office_users;
              })
              .catch(error => {
                /*console.log(error.response);*/
              });
    },
    getOfficeAdvisors(){
      axios.get('/task/advisor')
              .then(response => {
                this.office_advisors = response.data.office_users;
                this.office_advisors.$forceUpdate();

              })
              .catch(error => {
                /*console.log(error.response);*/
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
                /*console.log(error)*/
              });
    },
    deleteTask(index){
      if(confirm("Are you sure you want to delete this task?")) {
        toastr.success('<strong>Success!</strong> Task was deleted successfully.');

        toastr.options.timeOut = 1000;
        this.tasks.splice((index), 1);
        /*$(".task_" + index).hide();*/
      }
    },

    deleteSubtask(index,sub_index){
      if(confirm("Are you sure you want to delete this subtask?")) {
        toastr.success('<strong>Success!</strong> Subtask was deleted successfully.');

        toastr.options.timeOut = 1000;
        this.tasks[index].subtasks.splice((sub_index), 1);
        /*$(".task_" + index).hide();*/

        if (this.tasks[index].subtasks.length === 0) {
          this.tasks[index].add_sub_task = !this.tasks[index].add_sub_task;
        }
      }
    },
    assignTask(index){
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
          open14:false,
          selected_duedate:'',
          add_deadline: false,
        });
    },
    updateClient(clientName){
      if(this.card_form.updateClient === 0){
        this.card_form.updateClient = 1;
      } else {
        this.card_form.updateClient = 0;
      }
      this.card_form.client_name = clientName;
      this.addSectionFlag = false;
    },
    taskDeadlineSelected(index){

      const t = new Date(this.tasks[index].date).toISOString().slice(0, 10);

      this.tasks[index].assign_task = false;
      this.tasks[index].add_sub_task = false;
      this.tasks[index].selected_duedate = t;
      this.tasks[index].add_deadline = !this.tasks[index].add_deadline;
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
    closeCreateCard(){
      this.$emit('close-me')
    },
    autosaveCard(section){
      console.log(this.section.card_form.name);
      console.log(this.section.card_form.assignee_name);
      console.log(this.section.card_form.deadline);
      console.log(this.section.card_form.selected_deadline);
      console.log(this.section.card_form.progress_status_id);
      console.log(this.section.card_form.priority_status_id);
      let err = 0;
      if (this.section.card_form.name.length === 0){
        err++;
      }

      /*if (this.section.card_form.assignee_name.length === 0){
        err++;
      }

      if (this.section.card_form.deadline.length === 0){
        err++;
      }

      if (this.section.card_form.selected_deadline.length === 0){
        err++;
      }

      if (this.section.card_form.progress_status_id.length === 0){
        err++;
      }

      if (this.section.card_form.priority_status_id.length === 0){
        err++;
      }*/

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

      if(err > 0){
        //this.hasError = true;
        return false;
      } else {
        this.hasError = false;
      }
      axios.post('/card', {
        saved: 0,
        section: section,
        task: this.tasks,
        card_form: section.card_form,
        card_name: section.card_form.name,
        client_name: section.card_form.client_name
      })
              .then(response => {
                if(this.section.card_form.saved !== 0){

                } else
                {
                  this.section.card_form.saved = response.data.Card.id;
                }
              })
              .catch(function (error) {
                /*console.log(error.response);*/
              });
    },
    saveTask(section){
      let err = 0;
      if (this.section.card_form.name.length === 0){
        err++;
      }

      if (this.section.card_form.assignee_name.length === 0){
        err++;
      }

      if (this.section.card_form.deadline.length === 0){
        err++;
      }

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

      if(err > 0){
        this.hasError = true;
        return false;
      } else {
        this.hasError = false;
      }
        axios.post('/card', {
          saved: 1,
          section: section,
          task: this.tasks,
          card_form: section.card_form,
          card_name: section.card_form.name,
          client_name: section.card_form.client_name
        })
                .then(response => {

                    this.$emit('new-task', response.data.Card);

                    toastr.success('<strong>Success!</strong> Card was successfully created.');

                    toastr.options.timeOut = 1000;

                    this.$emit('close-me')
                })
                .catch(function (error) {
                  /*console.log(error.response);*/
                });
      },
    saveVoice(){
      let index = this.section.card_form.saved

      if(index !== 0) {
        var recording = document.getElementById('audioPlayer').src;
        var player = document.getElementById('audioFiles');
        var formData = new FormData();
        // var voice_recording = document.getElementById('audioPlayer');
        formData.append("audio", this.recordedAudio);
        formData.append("card_id", index);
        axios.post('/voice/record/' + index, formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          },
          // card_id: index,
          // audio: this.recordedAudio,
        })
                .then(response => {
                  toastr.success('<strong>Success!</strong> Voice recording was saved successfully.');

                  toastr.options.timeOut = 1000;
                  this.recordings.push({
                    card_id: response.data.recording.card_id,
                    user: response.data.recording.user,
                    recording: response.data.recording.recording
                  })
                })
                .catch(function (error) {
                  console.log(error.response);
                });
      } else {
        toastr.error('<strong>Error!</strong> Please complete all required case fields first.');

        toastr.options.timeOut = 1000;
        return false;
      }
    },
    // callback (data) {
    //     console.debug(data)
    //   },
    onResult (data) {
      /*console.log('The recorded data:', data);
      console.log('Downloadable audio', window.URL.createObjectURL(data));*/
      document.getElementById('audioPlayer').src = window.URL.createObjectURL(data);
      this.recordedAudio = data;
    },
    saveDiscussionMessage(){
      let card_id = this.section.card_form.saved

      if(card_id !== 0) {
        if (this.discussion_message.length === 0) {
          toastr.warning('<strong>Warning!</strong> Discussion message is required.');

          toastr.options.timeOut = 1000;
          return false;
        }

        axios.post('/discussion', {
          card_id: card_id,
          message: this.discussion_message
        }).then(response => {
          this.discussion_message = "";
          this.discussions.push(response.data.discussion);
          toastr.success('<strong>Success!</strong> Discussion successfully saved.');

          toastr.options.timeOut = 1000;
        }).catch(error => console.log(error.response));
      } else {
        toastr.error('<strong>Error!</strong> Please complete all required case fields first.');

        toastr.options.timeOut = 1000;
        return false;
      }
    }


  }
}
</script>

<style scoped>
  .card-discussion{
    background:#ecf1f4;
    padding: 0.7rem  0.7rem 1.5rem 0.7rem;
    margin:0 -0.7rem;
  }

  .card-recordings{
    padding:1.5rem;
    margin:0 -1.5rem;
  }

  .create-card{
    position: absolute;
    right: 0;
    top: 4.35rem;
    z-index: 10;
    background: #FFFFFF;
    height: calc(100vh - calc(70px + 3rem));
  }

  .create-card .card-body{
    padding: 0 0.7rem 0.7rem 0.7rem !important;
  }

  .mx-datepicker{
    width: 100% !important;
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
    height:calc(100% - 9rem);
    overflow: auto;
    overflow-x: hidden;
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
</style>