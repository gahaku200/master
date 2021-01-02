<template>
  <div>
    <p>Schedule</p>
    <p>goodGuys!!</p>
    <span class="btn-modal-close" @click="close($event)"></span>

    <div class="calendar-title">
      <span class="btn-monthMove prev fa fa-angle-left" @click="movePrevMonth()">＜</span>
      {{currentYear+"/"+currentMonth}}
      <span class="btn-monthMove next fa fa-angle-right" @click="moveNextMonth()">＞</span>
    </div>

    <table class="table">
      <thead>
        <tr>
          <th scope="col">日付</th>
          <th scope="col">通常勤務時間</th>
          <th scope="col">時間外</th>
          <th scope="col">深夜時間</th>
          <th scope="col">深夜時間外</th>
          <th scope="col">休憩時間</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="record in calendar">
          <th v-if="record.day !== '土'　&& record.day !== '日'" scope="row">{{ record.date }}({{ record.day }})</th>
          <th v-if="record.day === '土'" style="color: blue" scope="row">{{ record.date }}({{ record.day }})</th>
          <th v-if="record.day === '日'" style="color: red" scope="row">{{ record.date }}({{ record.day }})</th>
          <td>{{record.time}}</td>
          <td>{{record.overtime}}</td>
          <td>{{record.midnightTime}}</td>
          <td>{{record.midnightOvertime}}</td>
          <td>{{record.restTime}}</td>
        </tr>
      </tbody>
    </table>

    <button @click.stop.prevent="goHome()">戻る</button>
  </div>
</template>

<script>
export default {
  data() {
    return {
      today:"",
      selectedDay:"",
      currentYear:0,
      currentMonth:0,
      currentDate:0,
      weeks:["日","月","火","水","木","金","土"],
      calendar:[],
      performances: [],
      holidays:[],
      lastArray: '',
    };
  },
  created(){
    const date  = new Date();
    [this.currentYear,  this.currentMonth, this.currentDate] = [date.getFullYear(), date.getMonth() + 1, date.getDate()];
    this.today = this.selectedDay = `${this.currentYear}-${('0' + this.currentMonth).slice(-2)}-${this.currentDate}`;
  
  },
  mounted() {
    var id = document.querySelector("meta[name='user-id']").getAttribute('content');
    if (id <= 0) {
      this.$router.push("/");
    }
    this.calendarMaker();
   },
  methods:{
    chekcSelectedDay(day){
      return{
        'selectedDay':`${this.currentYear}-${('0' + this.currentMonth).slice(-2)}-${('0' + day).slice(-2)}` == this.selectedDay
      }
    },
    movePrevMonth(){
      this.currentMonth = this.currentMonth != 1 ? this.currentMonth - 1 : 12;
      this.currentYear = this.currentMonth != 12 ? this.currentYear : this.currentYear - 1;
      this.calendarMaker();
    },
    moveNextMonth(){
      this.currentMonth = this.currentMonth != 12 ? this.currentMonth + 1 : 1;
      this.currentYear = this.currentMonth != 1 ? this.currentYear : this.currentYear + 1;
      this.calendarMaker();
    },
    goHome() {
      this.$router.push("/");
    },
    calendarMaker() {
      var authId = document.querySelector("meta[name='user-id']").getAttribute('content');
      const theDay = new Date(this.currentYear, this.currentMonth - 1, 1);
      const toDay = new Date(this.currentYear, this.currentMonth, 1);
      this.performances = [];
      const data = {
      day: theDay,
      to_day: toDay
      }
      axios.post('/api/userAttendance/' + authId, data)
        .then(res =>  {
          //console.log(res.data);
          if (res.data != 'noData') {
            res.data.forEach((attendance) => {
              this.performances.push({on_duty: attendance.on_duty, time: attendance.time});
            })
          }
          this.calendar = [];
          const lastdate = new Date(this.currentYear, this.currentMonth, 0).getDate();
          for (let i = 1; i <= lastdate; i++) {
            const today = new Date(this.currentYear, this.currentMonth - 1, i);
            const nextDay = new Date(this.currentYear, this.currentMonth - 1, i + 1);

            const date = today.getDate();
            if (date < 10) {
              date = '0' + date;
            }
            const day = this.weeks[today.getDay()];

            var array = [];
            this.performances.forEach((performance) => {
              if (array.length == 0 && performance.on_duty == '出勤' && Date.parse(performance.time) >= today && Date.parse(performance.time) < nextDay) {
                array.push(performance);
                this.lastArray = performance.on_duty;
              } else if (array.length != 0 && Date.parse(performance.time) >= today && Date.parse(performance.time) < nextDay) {
                array.push(performance);
                this.lastArray = performance.on_duty;
              } else if (array.length != 0 && this.lastArray == '退勤' && Date.parse(performance.time) >= nextDay) {
                return true;
              } else if (array.length != 0 && this.lastArray != '退勤' && Date.parse(performance.time) >= nextDay) {
                array.push(performance);
                this.lastArray = performance.on_duty;
              }
            })

            console.log(i);
            if(array.length != 0) {
              var timetime = Date.parse(array[0].time);
              //console.log(array[0].time);
            }
            console.log(array);

            /*
            ☆一日のデータの時間抽出
            ・出勤→休入、退勤
            ・休入→休出
            ・休出→休入、退勤
            ・退勤→出勤
            □変数
            ・time
            ・overtime
            ・midnightTime
            ・midnightOvertime
            ・restTime
            ・X(AM5:00) am5.getTime()
            ・Y(PM22:00) pm10.getTime()
            ・past(前のデータ) array[x - 1].time 比較 Date.parse(array[x - 1].time)
            ・now(今のデータ) array[x].time 比較 Date.parse(array[x].time)
            */

            //const am5 = new Date(this.currentYear, this.currentMonth - 1, i, 5).getTime();
            //const pm10 = new Date(this.currentYear, this.currentMonth - 1, i, 22).getTime();
            //console.log(am5);
            //console.log(pm10);
            var time = 0;
            var overtime = 0;
            var midnightTime = 0
            var midnightOvertime = 0;
            var restTime = 0;
            const hours8 = 28800000;
            const hours24 = 86400000;

            if(array.length != 0) {
              for (let x = 1; x < array.length; x++) {
                var pastData = Date.parse(array[x - 1].time);
                var nowData = Date.parse(array[x].time);
                var am5 = new Date(this.currentYear, this.currentMonth - 1, i, 5).getTime();
                var pm10 = new Date(this.currentYear, this.currentMonth - 1, i, 22).getTime();
                var loopTime = Math.floor((nowData - today.getTime()) / (24 * 60 * 60 * 1000));
                //console.log(loopTime);
                //console.log(pastData - hours8);
                //console.log(array[x].time.getDate());

                if (array[x - 1].on_duty == '出勤' || array[x - 1].on_duty == '休出') {
                  if (pastData <= am5) {
                    if (nowData <= am5) {
                      if (time + midnightTime < hours8) {
                        midnightTime += nowData - pastData;
                        //console.log(midnightTime);
                        if (time + midnightTime >= hours8) {
                          midnightOvertime += time + midnightTime - hours8;
                          midnightTime -= time + midnightTime - hours8;
                          //console.log(9090);
                        }
                      } else {
                        midnightOvertime += nowData - pastData;
                        //console.log(9090);
                      }
                    } else if(nowData > am5 && nowData <= pm10) {
                      if (time + midnightTime < hours8) {
                        midnightTime += am5 - pastData;
                        if (time + midnightTime >= hours8) {
                          midnightOvertime += time + midnightTime - hours8;
                          midnightTime -= time + midnightTime - hours8;
                          //console.log(9090);
                        }
                        time += nowData - am5;
                        if (time + midnightTime >= hours8) {
                          overtime += time + midnightTime - hours8;
                          time -= time + midnightTime - hours8;
                        }
                      } else {
                        midnightOvertime += am5 - pastData;
                        overtime += nowData - am5;
                      }
                    } else {
                      if (loopTime == 0) {
                        if (time + midnightTime < hours8) {
                          midnightTime += am5 - pastData;
                          if (time + midnightTime > hours8) {
                            midnightOvertime += time + midnightTime - hours8;
                            midnightTime -= time + midnightTime - hours8;
                            //console.log(9090);
                          }
                          time += 61200000;
                          if (time + midnightTime >= hours8) {
                            overtime += time + midnightTime - hours8;
                            time -= time + midnightTime - hours8;
                          }
                          midnightOvertime += nowData - pm10;
                        } else {
                          midnightOvertime += am5 - pastData;
                          overtime += 61200000;
                          midnightOvertime += nowData - pm10;
                        }
                      } else {
                        for(let y = 1; y <= loopTime; y++) {
                          am5 += hours24;
                          pm10 += hours24;
                          if (y == loopTime) {
                            if (nowData <= am5) {
                              midnightOvertime += nowData - today.getTime() - (hours24 * y);
                            } else if (nowData > am5 && nowData <= pm10) {
                              midnightOvertime += 18000000;
                              overtime += nowData - am5;
                            } else {
                              midnightOvertime += 18000000;
                              overtime += 61200000;
                              midnightOvertime += nowData - pm10;
                            }
                          } else {
                            midnightOvertime += 18000000;
                            overtime += 61200000;
                            midnightOvertime += 7200000;
                          }
                        }
                      }
                    }
                  } else if (pastData > pm10) {
                    if (nowData < today + hours24) {
                      if (time + midnightTime < hours8) {
                        midnightTime += nowData - pastData;
                        if (time + midnightTime >= hours8) {
                          midnightOvertime += time + midnightTime - hours8;
                          midnightTime -= time + midnightTime - hours8;
                        }
                      } else {
                        midnightOvertime += nowData - pastData;
                      }
                    } else {
                      midnightTime += 7200000;
                      if (time + midnightTime >= hours8) {
                        midnightOvertime += time + midnightTime - hours8;
                        midnightTime -= time + midnightTime - hours8;
                      }
                      for(let y = 1; y <= loopTime; y++) {
                        am5 += hours24;
                        pm10 += hours24;
                        if (y == loopTime) {
                          if (nowData <= am5) {
                            midnightTime += nowData - today.getTime() - (hours24 * y);
                            if (time + midnightTime >= hours8) {
                              midnightOvertime += time + midnightTime - hours8;
                              midnightTime -= time + midnightTime - hours8;
                            }
                          } else if (nowData > am5 && nowData <= pm10) {
                            midnightTime += 18000000;
                            if (time + midnightTime >= hours8) {
                              midnightOvertime += time + midnightTime - hours8;
                              midnightTime -= time + midnightTime - hours8;
                            }
                            time += nowData - am5;
                            if (time + midnightTime >= hours8) {
                              overtime += time + midnightTime - hours8;
                              time -= time + midnightTime - hours8;
                            }
                          } else {
                            midnightTime += 18000000;
                            if (time + midnightTime >= hours8) {
                              midnightOvertime += time + midnightTime - hours8;
                              midnightTime -= time + midnightTime - hours8;
                            }
                            time += 61200000;
                            if (time + midnightTime >= hours8) {
                              overtime += time + midnightTime - hours8;
                              time -= time + midnightTime - hours8;
                            }
                            midnightOvertime += nowData - pm10;
                          }
                        } else {
                          midnightTime += 18000000;
                          if (time + midnightTime >= hours8) {
                            midnightOvertime += time + midnightTime - hours8;
                            midnightTime -= time + midnightTime - hours8;
                          }
                          time += 61200000;
                          if (time + midnightTime >= hours8) {
                            overtime += time + midnightTime - hours8;
                            time -= time + midnightTime - hours8;
                          }
                          midnightOvertime += 7200000;
                        }
                      }
                    }
                  } else {
                    if (nowData <= pm10) {
                      time += nowData - pastData;
                      if (time + midnightTime >= hours8) {
                        overtime += time + midnightTime - hours8;
                        time -= time + midnightTime - hours8;
                      }
                    } else {
                      time += pm10 - pastData;
                      if (time + midnightTime >= hours8) {
                        overtime += time + midnightTime - hours8;
                        time -= time + midnightTime - hours8;
                      }
                      midnightTime += 7200000;
                      if (time + midnightTime >= hours8) {
                        midnightOvertime += time + midnightTime - hours8;
                        midnightTime -= time + midnightTime - hours8;
                      }
                      for(let y = 1; y <= loopTime; y++) {
                        am5 += hours24;
                        pm10 += hours24;
                        if (y == loopTime) {
                          if (nowData <= am5) {
                            midnightTime += nowData - today.getTime() - (hours24 * y);
                            if (time + midnightTime >= hours8) {
                              midnightOvertime += time + midnightTime - hours8;
                              midnightTime -= time + midnightTime - hours8;
                            }
                          } else if (nowData > am5 && nowData <= pm10) {
                            midnightTime += 18000000;
                            if (time + midnightTime >= hours8) {
                              midnightOvertime += time + midnightTime - hours8;
                              midnightTime -= time + midnightTime - hours8;
                            }
                            time += nowData - am5;
                            if (time + midnightTime >= hours8) {
                              overtime += time + midnightTime - hours8;
                              time -= time + midnightTime - hours8;
                            }
                          } else {
                            midnightTime += 18000000;
                            if (time + midnightTime >= hours8) {
                              midnightOvertime += time + midnightTime - hours8;
                              midnightTime -= time + midnightTime - hours8;
                            }
                            time += 61200000;
                            if (time + midnightTime >= hours8) {
                              overtime += time + midnightTime - hours8;
                              time -= time + midnightTime - hours8;
                            }
                            midnightOvertime += nowData - pm10;
                          }
                        } else {
                          midnightTime += 18000000;
                          if (time + midnightTime >= hours8) {
                            midnightOvertime += time + midnightTime - hours8;
                            midnightTime -= time + midnightTime - hours8;
                          }
                          time += 61200000;
                          if (time + midnightTime >= hours8) {
                            overtime += time + midnightTime - hours8;
                            time -= time + midnightTime - hours8;
                          }
                          midnightOvertime += 7200000;
                        }
                      }
                    }
                  }
                } else if (array[x - 1].on_duty == '休入') {
                  restTime += nowData - pastData;
                }
              }
            }
            this.calendar.push({date: date, day: day, time: this.convertTime(Math.floor(time / 1000)), overtime: this.convertTime(Math.floor(overtime / 1000)), midnightTime: this.convertTime(Math.floor(midnightTime / 1000)), midnightOvertime: this.convertTime(Math.floor(midnightOvertime / 1000)), restTime: this.convertTime(Math.floor(restTime / 1000))});
          }
        }).catch( error => { console.log(error); })
    },
    convertTime(diffTime) {
      if (diffTime == 0) {
        return '-';
      }
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
  },
  computed:{
    calendarMake(){
      const firstday = new Date(this.currentYear, this.currentMonth - 1, 1).getDay();
      const lastdate = new Date(this.currentYear, this.currentMonth, 0).getDate();
      const necessarySpace = firstday == 0 ? 6 : firstday - 1;
      const list = [[...Array(necessarySpace)].map(i=>" "), [...Array(lastdate)].map((_, i) => i+1)];
      const week = list.reduce((pre,current) => {pre.push(...current);return pre},[]);
      return week;
    },
  }
};
</script>

