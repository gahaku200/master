<template>
  <div>
    <router-view :errors="errors" />
    <div v-if="groupId === null">
      <img class="img-fluid" src="https://picsum.photos/1500/610">
    </div>
    <div class="mainContainer" v-if="groupId > 0">
      <div class="container">
        <div class="row">
          <div class="col-lg-4"></div>
          <div class="col-lg-4 centerButton" v-if="type === 'A'">
            <button @click="startWork(), attendance()" type="button" class="btn btn-primary btn-lg">出勤</button>
          </div>
          <div class="col-lg-4 centerButton" v-if="type === 'C'">
            <button @click="preserveTaskTime(), startRest(), restName(), attendance()" type="button" class="btn btn-primary btn-lg">休入</button>
            <button @click="endWork(), taskEnd(), attendance()" type="button" class="btn btn-primary btn-lg">退勤</button>
          </div>
          <div class="col-lg-4 centerButton" v-if="type === 'B'">
            <button @click="endRest(), taskEnd(), attendance()" type="button" class="btn btn-primary btn-lg">休出</button>
          </div>
          <div class="col-lg-4"></div>

          <div class="col-lg-3"></div>
          <div class="col-lg-6" v-if="isTask">
            <form　class="form-now-task" ref="observer" action="/taskStart" id="taskStart" method="post" tag="form">
              <input type="hidden" name="_token" :value="csrf" />
              <div class="row">
                <div class="col-lg-8 kindOfTask">
                  <select v-model="selected" class="form-select" aria-label="Default select example" name="task">
                    <option disabled value="initial">タスクを選択してください</option>
                    <option v-for="task in tasks" name="task">{{ task.taskName }}</option>
                  </select>
                </div>
                <div class="col-lg-4 taskButton">
                  <button @click="taskStart(), preserveTaskTime()" type="button" class="btn btn-primary">タスク切替</button>
                </div>
              </div>
            </form>
          </div>
          <div class="col-lg-3"></div>

          <div class="col-lg-12 tasksShow">
              <div class="taskLabelContainer">
                <p v-if="taskLabel !== null" class="taskLabelshow">{{ taskLabel }}</p>
              </div>
              <div class="clock">
                <p class="timeshow">{{ doingTime }}</p>
              </div>
          </div>

          <div class="col-lg-4">
            <span class="d-block p-2 bg-primary text-white tableName">勤怠状況</span>
            <table class="table">
              <thead>
                <tr>
                  <th scope="col"></th>
                  <th scope="col">勤怠履歴</th>
                  <th scope="col">日時</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="attendance in attendanceRecords">
                  <td scope="row"></td>
                  <td style="padding-left: 24px">{{ attendance.on_duty }}</td>
                  <td>{{ attendance.time }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="col-lg-4">
            <span class="d-block p-2 bg-secondary text-white tableName">タスク履歴</span>
            <table class="table">
              <thead>
                <tr>
                  <th scope="col"></th>
                  <th scope="col">タスク名</th>
                  <th scope="col">合計時間</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="record in taskRecords">
                  <td scope="row"></td>
                  <td>{{ record.task_name }}</td>
                  <td>{{ record.task_time }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="col-lg-4">
            <span class="d-block p-2 bg-info text-white tableName">全ユーザータスク状況</span>
            <table class="table">
              <thead>
                <tr>
                  <th scope="col"></th>
                  <th scope="col">ユーザー名</th>
                  <th scope="col">タスク名</th>
                  <th scope="col">経過時間</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="user in users">
                  <td scope="row"></td>
                  <td>{{ user.name }}</td>
                  <td>{{ user.task }}</td>
                  <td>{{ user.time }}</td>
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
import vSelect from 'vue-select';

export default {
  props: {
    auth: {
      type: Object | Array
    },
    errors: {
      type: Object | Array
    }
  },
  data() {
    return {
      groupId: null,
      type: 'A',
      isTask: false,
      selected: 'initial',
      tasks: [],
      taskLabel: null,
      doingTime: '',
      taskTime: 0,
      taskRecords: [],
      attendanceRecords: [],
      users: [],
      on_duty: '',
      csrf: document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content"),
    }
  },
  mounted() {
    var authId = document.querySelector("meta[name='user-id']").getAttribute('content');
    var group_id = document.querySelector("meta[name='group-id']").getAttribute('content');
    if(group_id > 0) {
      this.groupId = group_id;
    }
    
    axios.get('/api/task/' + authId)
      .then(res =>  {
        this.taskRecords = [];
        res.data.forEach((task) => {
          this.taskRecords.push({task_name: task.task_name, task_time: this.convertTime(task.task_time)});
        })
      }).catch( error => { console.log(error); })

    axios.get('/api/attendance/' + authId)
      .then(res =>  {
        this.attendanceRecords = res.data;
        var duty = res.data[res.data.length - 1].on_duty;
        if (duty == '退勤') {
          this.type = 'A';
          this.isTask = false;
        } else if (duty == '休入') {
          this.type = 'B';
          this.isTask = false;
        } else {
          this.type = 'C';
          this.isTask = true;
        }
      }).catch( error => { console.log(error); })

    axios.get('/api/getKindOfTasks/' + group_id)
      .then(res =>  {
        this.tasks = [];
        res.data.forEach((task) => {
          this.tasks.push({taskName: task.taskName});
        })
      }).catch( error => { console.log(error); })

    if (group_id != '') {
      //this.getGroupInfo(authId, group_id);
      setInterval(() => {
      this.getGroupInfo(authId, group_id);
      }, 5000);
    }
   },
  methods: {
    getGroupInfo(authId, group_id) {
      axios.get('/api/getGroup/' + group_id)
        .then(res =>  {
          this.users = [];
          res.data.forEach((user) => {
            if (user.now_task_start == null) {
              var d2 = 0;
            } else {
              var d2 = new Date(user.now_task_start).getTime();
            }

            if (user.id == authId) {
              this.taskLabel = user.now_task;
              this.doingTime = this.passingTime(d2);
              this.passingTask(d2);
            } else {
              this.users.push({name: user.name, task: user.now_task, time: this.passingTime(d2)});
            }
          })
        }).catch( error => { console.log(error); })
    },
    restName() {
      const data = {
        task: '休憩'
      }
      var id = document.querySelector("meta[name='user-id']").getAttribute('content');
      axios.post('/api/taskStart/' + id, data)
      .catch( error => { console.log(error); });
    },
    taskStart() {
      if (this.selected != 'initial') {
        const data = {
        task: this.selected
        }
        var id = document.querySelector("meta[name='user-id']").getAttribute('content');
        axios.post('/api/taskStart/' + id, data).catch( error => { console.log(error); });
      }
    },
    preserveTaskTime() {
      if (this.taskLabel != '' && this.selected != 'initial') {
        const data = {
        task_name: this.taskLabel,
        task_time: this.taskTime
        }
        var id = document.querySelector("meta[name='user-id']").getAttribute('content');
        axios.post('/api/task/' + id, data)
        .then(res =>  {
          this.taskRecords = [];
          res.data.forEach((task) => {
            this.taskRecords.push({task_name: task.task_name, task_time: this.convertTime(task.task_time)});
          })
        })
        .catch( error => { console.log(error); });
        this.selected = 'initial';
      }
    },
    passingTime(d2) {
      if (d2 == 0) {
        return null;
      }
      var d1 = new Date();
      var diffTime = d1.getTime() - d2;
      return this.convertTime(Math.floor(diffTime / 1000));
    },
    passingTask(d2) {
      if (d2 == 0) {
        return null;
      }
      var d1 = new Date();
      var diffTime = d1.getTime() - d2;
      this.taskTime = Math.floor(diffTime / 1000);
    },
    attendance() {
      const data = {
      on_duty: this.on_duty
      }
      var id = document.querySelector("meta[name='user-id']").getAttribute('content');
      axios.post('/api/attendance/' + id, data)
      .then(res =>  {
        this.attendanceRecords = res.data;
        var duty = res.data[res.data.length - 1].on_duty;
        if (duty == '退勤') {
          this.type = 'A';
          this.isTask = false;
        } else if (duty == '休入') {
          this.type = 'B';
          this.isTask = false;
        } else {
          this.type = 'C';
          this.isTask = true;
        }
      })
      .catch( error => { console.log(error); });
    },
    startWork() {
      this.on_duty = '出勤';
      this.taskRecords = [];
    },
    startRest() {
      this.on_duty = '休入';
      const data = {
      task_name: this.taskLabel,
      task_time: this.taskTime
      }
      var id = document.querySelector("meta[name='user-id']").getAttribute('content');
      axios.post('/api/task/' + id, data)
      .then(res =>  {
        this.taskRecords = [];
        res.data.forEach((task) => {
          this.taskRecords.push({task_name: task.task_name, task_time: this.convertTime(task.task_time)});
        })
      })
      .catch( error => { console.log(error); });
      this.selected = 'initial';
    },
    endRest() {
      this.on_duty = '休出';
    },
    endWork() {
      if (this.taskLabel != '') {
        const data = {
        task_name: this.taskLabel,
        task_time: this.taskTime
        }
        var id = document.querySelector("meta[name='user-id']").getAttribute('content');
        axios.post('/api/task/' + id, data)
        .then(res =>  {
          this.taskRecords = [];
          res.data.forEach((task) => {
            this.taskRecords.push({task_name: task.task_name, task_time: this.convertTime(task.task_time)});
          })
        })
        .catch( error => { console.log(error); });
        this.selected = 'initial';
      }
      this.on_duty = '退勤'
    },
    taskEnd() {
      var id = document.querySelector("meta[name='user-id']").getAttribute('content');
      axios.post('/api/taskEnd/' + id)
      .catch( error => { console.log(error); });
    },
    convertTime(diffTime) {
      var diffHour = Math.floor(diffTime / (60 * 60));
      if(diffHour < 10) {
        diffHour = '0' + diffHour;
      }
      var diffMinites = Math.floor(diffTime / 60 % 60);
      if(diffMinites < 10) {
        diffMinites = '0' + diffMinites;
      }
      var diffSeconds = Math.floor(diffTime % 60);
      if(diffSeconds < 10) {
        diffSeconds = '0' + diffSeconds;
      }
      var passTime = diffHour + ':' + diffMinites + ':' + diffSeconds;
      return passTime;
    },
  }
};
</script>