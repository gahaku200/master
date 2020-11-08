<template>
  <div id="home">
    <router-view :errors="errors" />
    <div class="mainContainer">
      <div class="container">
        <div class="row">
          <div class="col-lg-4"></div>
          <div class="col-lg-4 centerButton" div v-if="type === 'A'">
            <button @click="changeB(), taskOpen(), start(), startWork()" type="button" class="btn btn-primary btn-lg">出勤</button>
          </div>
          <div class="col-lg-4 centerButton" div v-if="type === 'B'">
            <button @click="changeC(), startRest(), stop(), reset(), start(), taskOpen(), restName(),changeSB()" type="button" class="btn btn-primary btn-lg">休入</button>
            <button @click="changeA(), endWork(), stop(), reset(), emptytask(), taskOpen()" type="button" class="btn btn-primary btn-lg">退勤</button>
          </div>
          <div class="col-lg-4 centerButton" div v-if="type === 'C'">
            <button @click="changeB(), endRest(), stop(), reset(), start(), taskOpen(), emptytask()" type="button" class="btn btn-primary btn-lg">休出</button>
          </div>
          <div class="col-lg-4"></div>

          <div class="col-lg-3"></div>
          <div class="col-lg-6">
            <div v-if="isTask" class="row">
              <div class="col-lg-8 kindOfTask">
                <select v-model="selected" class="form-select" aria-label="Default select example">
                  <option disabled value="initial">タスクを選択してください</option>
                  <option v-for="task in tasks">{{ task }}</option>
                </select>
              </div>
              <div class="col-lg-4 taskButton">
                <button @click="taskName(), stop(), reset(), start(),changeSB()" type="button" class="btn btn-primary">タスク切替</button>
              </div>
            </div>
          </div>
          <div class="col-lg-3"></div>

          <div v-if="isTime" class="col-lg-12 tasksShow">
              <div class="taskLabelContainer">
                <p class="taskLabelshow">{{ taskLabel }}</p>
              </div>
              <div class="clock">
                <p class="timeshow">{{ formattedElapsedTime }}</p>
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
                <tr>
                  <td scope="row"></td>
                  <td>ピカチュウ</td>
                  <td>資料作成</td>
                  <td>00:12:32</td>
                </tr>
                <tr>
                  <td scope="row"></td>
                  <td>ヒドカゲ</td>
                  <td>動画編集</td>
                  <td>00:35:46</td>
                </tr>
                <tr>
                  <td scope="row"></td>
                  <td>ゼニガメ</td>
                  <td>会議</td>
                  <td>02:12:03</td>
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
      taskLabel: '',
      count: 0,
      currentTime: new Date(),
      elapsedTime: 0,
      timer: undefined,
      isTime: false,
      startTime: '00:00:00',
      startRestTime: '00:00:00',
      endRestTime: '00:00:00',
      endTime: '00:00:00',
      records: [],
      attendanceRecords: [],
    }
  },
  computed: {
    formattedElapsedTime() {
      const date = new Date(null);
      date.setSeconds(this.elapsedTime / 1000);
      const utc = date.toUTCString();
      return utc.substr(utc.indexOf(":") - 2, 8);
    }
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
    start() {
      this.timer = setInterval(() => {
        this.elapsedTime += 1000;
      }, 1000);
    },
    stop() {
      clearInterval(this.timer);
    },
    reset() {
      this.elapsedTime = 0;
    },
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
      this.taskLabel = '休憩';
      this.isTime = true;
    },
    emptytask() {
      this.taskLabel = '';
      this.isTime = false;
      this.count = 0;
    },
    changeSB() {
      this.selected = 'initial';
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
  }
};
</script>