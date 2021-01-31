<template>
  <div>
    <div style="text-align: center;" class="taskDetailTitle">
      <h3 style="padding-top: 10px;">{{ userName }}さんのタスク実績表</h3>
    </div>
    <div class="calendar-title" style="display: flex; justify-content: center">
      <h2 style="padding: 10px">{{ year }}/{{ month }}{{ theDate }}</h2>
    </div>

    <div v-if="!searchSwitch" class="searchDay" style="text-align: center; margin-bottom: 10px;">
      <v-date-picker v-model="date">
        <template v-slot="{ inputValue, inputEvents }">
          <input
            class="bg-white border px-2 py-1 rounded"
            :value="inputValue"
            v-on="inputEvents"
          />
        </template>
      </v-date-picker>
      <button @click="searchTaskDay()" type="button" class="btn btn-outline-primary">検索</button>
      <button @click="switchItem()" type="button" class="btn btn-outline-secondary">月単位</button>
    </div>

    <div v-if="searchSwitch" class="searchMonth" style="display: flex; justify-content: center; margin-bottom: 10px;">
      <div class="selectYear" style="display: flex">
        <select v-model="selectedYear" class="form-select" aria-label="Default select example">
          <option disabled value="initial">年</option>
          <option value="2020">2020</option>
          <option value="2021">2021</option>
          <option value="2022">2022</option>
        </select>
        <p style="margin: auto">年</p>
      </div>
      <div class="selectMonth" style="display: flex">
        <select v-model="selectedMonth" class="form-select" aria-label="Default select example">
          <option disabled value="initial">月</option>
          <option value="01">1</option>
          <option value="02">2</option>
          <option value="03">3</option>
          <option value="04">4</option>
          <option value="05">5</option>
          <option value="06">6</option>
          <option value="07">7</option>
          <option value="08">8</option>
          <option value="09">9</option>
          <option value="10">10</option>
          <option value="11">11</option>
          <option value="12">12</option>
        </select>
        <p style="margin: auto">月</p>
      </div>
      <div class="searchButton">
        <button @click="searchTaskMonth()" type="button" class="btn btn-outline-primary">検索</button>
        <button @click="switchItem()" type="button" class="btn btn-outline-secondary">日単位</button>
      </div>
    </div>

    <div v-if="taskRecords.length === 0" class="noRecord">
      <h3 style="text-align: center; color: tomato;">検索結果がありません</h3>
    </div>

    <div v-if="taskRecords.length !== 0" class="container">
      <div class="row">
        <div class="col-lg-3"></div>
        <div class="col-lg-6">
          <span class="d-block p-2 bg-secondary text-white tableName">タスク履歴</span>
          <table class="table">
            <thead>
              <tr>
                <th scope="col"></th>
                <th scope="col">タスク名</th>
                <th scope="col">合計時間</th>
                <th scope="col">割合</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="record in taskRecords">
                <td scope="row"></td>
                <td>{{ record.task_name }}</td>
                <td>{{ record.task_time }}</td>
                <td>{{ record.task_percentage }}%</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="col-lg-3"></div>
      </div>
    </div>
    <div style="text-align:center" class="homeButton">
      <button @click.stop.prevent="goHome()" type="button" class="btn btn-outline-info">ホームへ戻る</button>
    </div>
  </div>
</template>

<script>
import Vue from 'vue';
import VCalendar from 'v-calendar';
import { ValidationObserver } from 'vee-validate';
Vue.use(VCalendar);

export default {
  data() {
    return {
      userName: this.$route.params.name,
      date: new Date(),
      year:0,
      month:0,
      theDate: '',
      searchSwitch: true,
      selectedYear: 'initial',
      selectedMonth: 'initial',
      taskRecordsInt: [],
      taskRecords: [],
      total: 0,
    };
  },
  created(){
    const date  = new Date();
    [this.year, this.month] = [date.getFullYear(), ("0"+(date.getMonth() + 1)).slice(-2)];
  },
  mounted() {
    if (this.$route.params.id == null) {
      this.$router.go(-1);
    }
    var from_day = this.year + '-' + this.month + '-01 00:00:00';
    if (this.month == 12) {
      var theYear = Number(this.year) + 1;
      var theMonth = '01';
    } else {
      var theYear = this.year;
      var theMonth = ("0"+(Number(this.month) + 1)).slice(-2);
    }
    var to_day = theYear + '-' + theMonth + '-01 00:00:00';
    this.searchDetail(from_day, to_day);
  },
  methods: {
    switchItem() {
      this.searchSwitch = !this.searchSwitch;
      this.year = '';
      this.month = '';
      this.theDate = '';
    },
    searchTaskMonth() {
      if (this.selectedYear != 'initial' && this.selectedMonth != 'initial') {
        var from_day = this.selectedYear + '-' + this.selectedMonth + '-01 00:00:00';
        if (this.selectedMonth == 12) {
          var theYear = Number(this.selectedYear) + 1;
          var theMonth = '01';
        } else {
          var theYear = this.selectedYear;
          var theMonth = ("0"+(Number(this.selectedMonth) + 1)).slice(-2);
        }
        var to_day = theYear + '-' + theMonth + '-01 00:00:00';

        this.searchDetail(from_day, to_day);
        this.year = this.selectedYear;
        this.month = this.selectedMonth;
        this.selectedYear = 'initial';
        this.selectedMonth = 'initial';
      }
    },
    searchTaskDay() {
      var theMonth = ("0"+(Number(this.date.getMonth()) + 1)).slice(-2);
      var theDate = ("0"+this.date.getDate()).slice(-2);
      var from_day = this.date.getFullYear() + '-' + theMonth + '-' + theDate + ' 00:00:00';

      var nextDay = new Date(this.date.getFullYear(), this.date.getMonth(), (Number(theDate) + 1));
      var nextDayMonth = ("0"+(Number(nextDay.getMonth()) + 1)).slice(-2);
      var nextDayDate = ("0"+nextDay.getDate()).slice(-2);
      var to_day = nextDay.getFullYear() + '-' + nextDayMonth + '-' + nextDayDate + ' 00:00:00';

      this.searchDetail(from_day, to_day);
      this.year = this.date.getFullYear();
      this.month = theMonth;
      this.theDate = '/' + theDate;
    },
    searchDetail(from_day, to_day) {
      const data = {
        fromDay: from_day,
        toDay: to_day,
      }
      var authId = this.$route.params.id;
      axios.post('/api/taskDetail/' + authId, data)
        .then(res =>  {
          this.taskRecordsInt = [];
          this.total = 0;
          var i = 0;
          res.data.forEach((task) => {
            this.total += Number(task.task_time);
            if (i == 0) {
              this.taskRecordsInt.push({task_name: task.task_name, task_time: task.task_time});
            } else {
              var j = 0;
              this.taskRecordsInt.forEach((record) => {
                if (record.task_name == task.task_name) {
                  record.task_time = Number(record.task_time) + Number(task.task_time);
                  j++;
                  return true;
                }
              })
              if (j == 0) {
                this.taskRecordsInt.push({task_name: task.task_name, task_time: task.task_time});
              }
            }
            i++;
          })
          this.taskRecordsInt.sort(function (a, b) {
            return b.task_time - a.task_time;
          });
          this.adjustArray();
        }).catch( error => { console.log(error); })
    },
    adjustArray() {
      this.taskRecords = [];
      if (this.taskRecordsInt.length > 0) {
        this.taskRecordsInt.forEach((record) => {
          var percentage = Math.round(Number(record.task_time) / this.total * 100 * 10) / 10;
          this.taskRecords.push({task_name: record.task_name, task_time: this.convertTime(record.task_time), task_percentage: percentage});
        })
      }
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
    goHome() {
      this.$router.push("/");
    }
  },
};
</script>