<template>
  <div>
    <router-view :errors="errors" />
    <div v-if="auth.length === 0">
      <img class="img-fluid" src="https://picsum.photos/1500/610">
    </div>
    <div class="mainContainer" v-if="auth.length !== 0">
      <div class="container">
        <div class="row">
          <div class="col-lg-4"></div>
          <div class="col-lg-4 centerButton" div v-if="type === 'A'">
            <button @click="changeB(), taskOpen(),startWork()" type="button" class="btn btn-primary btn-lg">出勤</button>
          </div>
          <div class="col-lg-4 centerButton" div v-if="type === 'B'">
            <button @click="changeC(), startRest(), taskOpen(), restName()" type="button" class="btn btn-primary btn-lg">休入</button>
            <button @click="changeA(), endWork(), emptytask(), taskOpen()" type="button" class="btn btn-primary btn-lg">退勤</button>
          </div>
          <div class="col-lg-4 centerButton" div v-if="type === 'C'">
            <button @click="changeB(), endRest(), taskOpen(), emptytask()" type="button" class="btn btn-primary btn-lg">休出</button>
          </div>
          <div class="col-lg-4"></div>

          <div class="col-lg-3"></div>
          <div class="col-lg-6">
            <div v-if="isTask" class="row">
              <form　class="form-now-task" ref="observer" action="/taskStart" id="taskStart" method="post" tag="form">
                <input type="hidden" name="_token" :value="csrf" />
                <div class="col-lg-8 kindOfTask">
                  <select v-model="selected" class="form-select" aria-label="Default select example" name="task">
                    <option disabled value="initial">タスクを選択してください</option>
                    <option v-for="task in tasks" name="task">{{ task }}</option>
                  </select>
                </div>
                <div class="col-lg-4 taskButton">
                  <button @click="taskStart()" type="button" class="btn btn-primary">タスク切替</button>
                </div>
              </form>
            </div>
          </div>
          <div class="col-lg-3"></div>

          <div class="col-lg-12 tasksShow">
              <div class="taskLabelContainer">
                <p class="taskLabelshow">{{ taskLabel }}</p>
              </div>
              <div class="clock">
                <p class="timeshow">{{ doingTime }}</p>
              </div>
          </div>

          <div class="col-lg-4">
            <span class="d-block p-2 bg-primary text-white tableName">本日の勤怠状況</span>
            <table class="table">
              <thead>
                <tr>
                  <th scope="col"></th>
                  <th scope="col">勤怠履歴</th>
                  <th scope="col">時刻</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="attendance in attendanceRecords">
                  <td scope="row"></td>
                  <td>{{ attendance.name }}</td>
                  <td>{{ attendance.time }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="col-lg-4">
            <span class="d-block p-2 bg-secondary text-white tableName">本日のタスク履歴</span>
            <table class="table">
              <thead>
                <tr>
                  <th scope="col"></th>
                  <th scope="col">タスク名</th>
                  <th scope="col">合計時間</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="record in records">
                  <td scope="row"></td>
                  <td>{{ record.name }}</td>
                  <td>{{ record.totalTime }}</td>
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
      type: 'A',
      isTask: false,
      selected: 'initial',
      tasks: ['メールチェック', '朝礼', '作業', '会議', '研修', '外出', '離席', 'その他'],
      taskLabel: 'タスク状況',
      count: 0,
      //currentTime: new Date(),
      //elapsedTime: 0,
      //timer: undefined,
      isTime: false,
      //startTime: '00:00:00',
      //startRestTime: '00:00:00',
      //endRestTime: '00:00:00',
      //endTime: '00:00:00',
      doingTime: '00:00:00',
      records: [],
      attendanceRecords: [],
      users: [],
      csrf: document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content"),
    }
  },
  mounted() {
    var authId = document.querySelector("meta[name='user-id']").getAttribute('content');
    var group_id = document.querySelector("meta[name='group-id']").getAttribute('content');

    if (group_id != '') {
      setInterval(() => {
      axios.get('/api/getGroup/' + group_id)
        .then(res =>  {
          this.users = [];
          res.data.forEach((user) => {
            if (user.id == authId) {
              this.taskLabel = user.now_task;
              this.doingTime = this.passingTime(user.now_task_start);
            } else {
              this.users.push({name: user.name, task: user.now_task, time: this.passingTime(user.now_task_start)});
            }
          })
        }).catch( error => { console.log(error); })
      }, 1000);
    }
   },
  computed: {
    /*formattedElapsedTime() {
      const date = new Date(null);
      date.setSeconds(this.elapsedTime / 1000);
      const utc = date.toUTCString();
      return utc.substr(utc.indexOf(":") - 2, 8);
    },*/
    groupMember() {
      var members = this.users.filter(e => e.group_id == auth.group_id);
      return members;
    },
    
  },
  methods: {
    changeA() {
      this.type = 'A'
    },
    changeB() {
      this.type = 'B'
    },
    changeC() {
      this.type = 'C'
    },
    taskOpen() {
      this.isTask = !this.isTask
    },
    /*start() {
      this.timer = setInterval(() => {
        this.elapsedTime += 1000;
      }, 1000);
    },
    stop() {
      clearInterval(this.timer);
    },
    reset() {
      this.elapsedTime = 0;
    },*/
    taskName() {
      if (this.count != 0 && this.selected != 'initial') {
        this.records.push({name: this.taskLabel, totalTime: this.formattedElapsedTime})
      }

      if (this.count != 0 && this.selected == 'initial'){
        this.records.push({name: this.taskLabel, totalTime: this.formattedElapsedTime})
        this.taskLabel = '';
        this.isTime = false;
        this.count = 0;
      } else if (this.selected == 'initial') {
        return;
      } else {
        this.taskLabel = this.selected;
        this.isTime = true;
        this.count = 1;
      }
    },
    restName() {
      const data = {
        task: '休憩'
      }
      var self = this;
      var id = document.querySelector("meta[name='user-id']").getAttribute('content');
      axios.post('/api/taskStart/' + id, data)
      .catch( error => { console.log(error); });
    },
    emptytask() {
      this.taskLabel = '';
      this.isTime = false;
      this.count = 0;
    },
    startWork() {
      this.records = [];
      var date = new Date();
      var gmt = date.toString();
      this.startTime = gmt.substr(gmt.indexOf(":") - 2, 8);
      this.attendanceRecords.push({name: '出勤', time: this.startTime})
    },
    startRest() {
      if (this.taskLabel != '') {
        this.records.push({name: this.taskLabel, totalTime: this.formattedElapsedTime})
      }
      var date = new Date();
      var gmt = date.toString();
      this.startRestTime = gmt.substr(gmt.indexOf(":") - 2, 8);
      this.attendanceRecords.push({name: '休入', time: this.startRestTime})
    },
    endRest() {
      this.records.push({name: this.taskLabel, totalTime: this.formattedElapsedTime})
      var date = new Date();
      var gmt = date.toString();
      this.endRestTime = gmt.substr(gmt.indexOf(":") - 2, 8);
      this.attendanceRecords.push({name: '休出', time: this.endRestTime})
    },
    endWork() {
      if (this.taskLabel != '') {
        this.records.push({name: this.taskLabel, totalTime: this.formattedElapsedTime})
      }
      var date = new Date();
      var gmt = date.toString();
      this.endTime = gmt.substr(gmt.indexOf(":") - 2, 8);
      this.attendanceRecords.push({name: '退勤', time: this.endTime})
    },
    taskStart() {
      if (this.selected != 'initial') {
        const data = {
        task: this.selected
        }
        var id = document.querySelector("meta[name='user-id']").getAttribute('content');
        axios.post('/api/taskStart/' + id, data)
        .catch( error => { console.log(error); });
        
        this.selected = 'initial';
      } else {
      }
    },
    passingTime(nowTaskStart) {
      if (nowTaskStart == null) {
        return nowTaskStart;
      }
      var d1 = new Date();
      var d2 = new Date(nowTaskStart);

      var diffTime = d1.getTime() - d2.getTime();

      var diffHour = Math.floor(diffTime / (1000 * 60 * 60));
      if(diffHour < 10) {
        diffHour = '0' + diffHour;
      }
      var diffMinites = Math.floor(diffTime / (1000 * 60) % 60);
      if(diffMinites < 10) {
        diffMinites = '0' + diffMinites;
      }
      var diffSeconds = Math.floor(diffTime / 1000 % 60);
      if(diffSeconds < 10) {
        diffSeconds = '0' + diffSeconds;
      }

      var passTime = diffHour + ':' + diffMinites + ':' + diffSeconds;
      return passTime;
    }
  }
};
</script>