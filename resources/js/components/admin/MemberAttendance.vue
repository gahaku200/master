<template>
  <div>
    <div style="text-align: center;" class="ResultOfAttendanceTitle">
      <h3 style="padding-top: 10px;">{{ userName }}さんの勤務実績表</h3>
    </div>

    <div class="calendar-title" style="display: flex; justify-content: center">
      <span class="btn-monthMove prev fa fa-angle-left" style="padding-top: 12px">
        <button @click="movePrevMonth()" type="button" class="btn btn-outline-primary">＜</button>
      </span>
      <h2 style="padding: 10px">{{currentYear+"/"+currentMonth}}</h2>
      <span class="btn-monthMove next fa fa-angle-right" style="padding-top: 12px">
        <button @click="moveNextMonth()" type="button" class="btn btn-outline-primary">＞</button>
      </span>
    </div>

    <div style="text-align: center;" class="ResultOfAttendanceTotal">
      <div class="badge rounded-pill bg-success">
        <h4 style="padding: 10px 10px 5px 10px;">合計勤務時間：{{ total }}</h4>
      </div>
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
          <th v-if="record.day !== '土'　&& record.day !== '日'" scope="row">
            <router-link class="weekdays" :to="{name:'memberAttendanceDetail',params:{id: id, name: userName, theDay: record.theDay}}">
              {{ record.date }}({{ record.day }})
            </router-link>
          </th>
          <th v-if="record.day === '土'" scope="row">
            <router-link class="saturday" :to="{name:'memberAttendanceDetail',params:{id: id, name: userName, theDay: record.theDay}}">
              {{ record.date }}({{ record.day }})
            </router-link>
          </th>
          <th v-if="record.day === '日'" scope="row">
            <router-link class="sunday" :to="{name:'memberAttendanceDetail',params:{id: id, name: userName, theDay: record.theDay}}">
              {{ record.date }}({{ record.day }})
            </router-link>
          </th>
          <td>{{record.time}}</td>
          <td>{{record.overtime}}</td>
          <td>{{record.midnightTime}}</td>
          <td>{{record.midnightOvertime}}</td>
          <td>{{record.restTime}}</td>
        </tr>
        <tr>
          <th>合計時間</th>
          <td>{{ totalTime }}</td>
          <td>{{ totalOvertime }}</td>
          <td>{{ totalMidnightTime }}</td>
          <td>{{ totalMidnightOvertime }}</td>
          <td>{{ totalRestTime }}</td>
        </tr>
      </tbody>
    </table>

    <div style="text-align:center; padding-top: 20px;" class="homeButton">
      <button @click.stop.prevent="goHome()" type="button" class="btn btn-outline-info">ホームへ戻る</button>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      id: this.$route.params.id,
      userName: this.$route.params.name,
      currentYear:0,
      currentMonth:0,
      currentDate:0,
      weeks:["日","月","火","水","木","金","土"],
      calendar:[],
      performances: [],
      holidays:[],
      lastArray: '',
      totalTimeInt: 0,
      totalOvertimeInt: 0,
      totalMidnightTimeInt: 0,
      totalMidnightOvertimeInt: 0,
      totalRestTimeInt: 0,
      totalTime: '',
      totalOvertime: '',
      totalMidnightTime: '',
      totalMidnightOvertime: '',
      totalRestTime: '',
      total: '',
    };
  },
  created(){
    const date  = new Date();
    [this.currentYear,  this.currentMonth, this.currentDate] = [date.getFullYear(), date.getMonth() + 1, date.getDate()];
  },
  mounted() {
    var id = this.$route.params.id;
    if (id == null) {
      this.$router.push("/groupMember");
    }
    this.calendarMaker();
   },
  methods:{
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
      this.totalTimeInt = 0;
      this.totalOvertimeInt = 0;
      this.totalMidnightTimeInt = 0;
      this.totalMidnightOvertimeInt = 0;
      this.totalRestTimeInt = 0;
      var authId = this.$route.params.id;
      const theDay = new Date(this.currentYear, this.currentMonth - 1, 1);
      const toDay = new Date(this.currentYear, this.currentMonth, 1);
      const theDay2 = new Date(this.currentYear, this.currentMonth - 1, 1, 9);
      const toDay2 = new Date(this.currentYear, this.currentMonth, 1, 9);
      this.performances = [];
      const data = {
        day: theDay2,
        to_day: toDay2
      }
      axios.post('/api/userAttendance/' + authId, data)
        .then(res =>  {
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

                if (array[x - 1].on_duty == '出勤' || array[x - 1].on_duty == '休出') {
                  var resultArray = this.detailCalc(today, time, overtime, midnightTime, midnightOvertime, hours8, hours24, pastData, nowData, am5, pm10,loopTime);
                  time = resultArray[0];
                  overtime = resultArray[1];
                  midnightTime = resultArray[2];
                  midnightOvertime = resultArray[3];
                } else if (array[x - 1].on_duty == '休入') {
                  restTime += nowData - pastData;
                }
              }
            }
            this.totalTimeInt += time;
            this.totalOvertimeInt += overtime;
            this.totalMidnightTimeInt += midnightTime;
            this.totalMidnightOvertimeInt += midnightOvertime;
            this.totalRestTimeInt += restTime;

            var params1 = this.currentYear + ',' + this.currentMonth + ',' + i;

            this.calendar.push({
              date: date,
              day: day,
              time: this.convertTime(Math.floor(time / 1000)),
              overtime: this.convertTime(Math.floor(overtime / 1000)),
              midnightTime: this.convertTime(Math.floor(midnightTime / 1000)),
              midnightOvertime: this.convertTime(Math.floor(midnightOvertime / 1000)),
              restTime: this.convertTime(Math.floor(restTime / 1000)),
              theDay: params1,
              });
          }
          this.totalTime = this.convertTime(Math.floor(this.totalTimeInt / 1000));
          this.totalOvertime = this.convertTime(Math.floor(this.totalOvertimeInt / 1000));
          this.totalMidnightTime = this.convertTime(Math.floor(this.totalMidnightTimeInt / 1000));
          this.totalMidnightOvertime = this.convertTime(Math.floor(this.totalMidnightOvertimeInt / 1000));
          this.totalRestTime = this.convertTime(Math.floor(this.totalRestTimeInt / 1000));
          this.total = this.convertTime(Math.floor((this.totalTimeInt + this.totalOvertimeInt + this.totalMidnightTimeInt + this.totalMidnightOvertimeInt)/ 1000));
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
    detailCalc(theDay, time, overtime, midnightTime, midnightOvertime, hours8, hours24, pastData, nowData, am5, pm10,loopTime) {
      if (pastData <= am5) {
        if (nowData <= am5) {
          if (time + midnightTime < hours8) {
            midnightTime += nowData - pastData;
            if (time + midnightTime >= hours8) {
              midnightOvertime += time + midnightTime - hours8;
              midnightTime -= time + midnightTime - hours8;
            }
          } else {
            midnightOvertime += nowData - pastData;
          }
        } else if(nowData > am5 && nowData <= pm10) {
          if (time + midnightTime < hours8) {
            midnightTime += am5 - pastData;
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
              if (loopTime == 1) {
                midnightTime = am5 - hours24 - pastData;
                time = hours8 - midnightTime;
                overtime = pm10 - am5 - time;
                midnightOvertime += 7200000;
                if (nowData <= am5) {
                  midnightOvertime += nowData - theDay.getTime() - (hours24 * y);
                } else if (nowData > am5 && nowData <= pm10) {
                  midnightOvertime += 18000000;
                  overtime += nowData - am5;
                } else {
                  midnightOvertime += 18000000;
                  overtime += 61200000;
                  midnightOvertime += nowData - pm10;
                }
              } else if (y == 1) {
                midnightTime = am5 - hours24 - pastData;
                time = hours8 - midnightTime;
                overtime = pm10 - am5 - time;
                midnightOvertime += 7200000;
              } else if (y == loopTime) {
                midnightOvertime += 18000000;
                overtime += 61200000;
                midnightOvertime += 7200000;
                if (nowData <= am5) {
                  midnightOvertime += nowData - theDay.getTime() - (hours24 * y);
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
        if (nowData < theDay.getTime() + hours24) {
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
          if (pastData < theDay.getTime() + hours24) {
            midnightTime += (theDay.getTime() + hours24 - pastData);
            if (time + midnightTime >= hours8) {
              midnightOvertime += time + midnightTime - hours8;
              midnightTime -= time + midnightTime - hours8;
            }
            for(let y = 1; y <= loopTime; y++) {
              am5 += hours24;
              pm10 += hours24;
              if (y == loopTime) {
                if (nowData <= am5) {
                  midnightTime += nowData - theDay.getTime() - (hours24 * y);
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
          } else {
            am5 += hours24;
            pm10 += hours24;
            loopTime--;
            theDay.setDate(theDay.getDate() + 1);
            var resultArray = this.detailCalc(theDay, time, overtime, midnightTime, midnightOvertime, hours8, hours24, pastData, nowData, am5, pm10,loopTime);
            time = resultArray[0];
            overtime = resultArray[1];
            midnightTime = resultArray[2];
            midnightOvertime = resultArray[3];

          }
        }
      } else {
        if (nowData <= pm10) {
          time += nowData - pastData;
          if (time + midnightTime >= hours8) {
            overtime += time + midnightTime - hours8;
            time -= time + midnightTime - hours8;
          }
        } else if (nowData <= theDay.getTime() + hours24) {
          time += pm10 - pastData;
          if (time + midnightTime >= hours8) {
            overtime += time + midnightTime - hours8;
            time -= time + midnightTime - hours8;
          }
          midnightTime += nowData - pm10;
          if (time + midnightTime >= hours8) {
            midnightOvertime += time + midnightTime - hours8;
            midnightTime -= time + midnightTime - hours8;
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
                midnightTime += nowData - theDay.getTime() - (hours24 * y);
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
      return [time, overtime, midnightTime, midnightOvertime];
    },
  },
};
</script>