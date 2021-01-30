<template>
  <div>
    <div style="text-align: center;" class="attendanceDetailTitle">
      <h3 style="padding-top: 10px;">勤務詳細</h3>
    </div>
    <h2 style="text-align: center">{{detailDate}}(
      <nobr v-if="detailDay !== '土'　&& detailDay !== '日'">{{detailDay}}</nobr>
      <nobr v-if="detailDay === '土'" style="color: blue">{{detailDay}}</nobr>
      <nobr v-if="detailDay === '日'" style="color: red">{{detailDay}}</nobr>
    )</h2>
    <div class="container">
      <div class="row">
        <div class="col-lg-3"></div>
        <div class="col-lg-6">
          <span class="d-block p-2 bg-primary text-white tableName">勤怠状況</span>
          <table class="table">
            <thead>
              <tr>
                <th scope="col"></th>
                <th scope="col">勤怠履歴</th>
                <th scope="col">日時</th>
                <th scope="col">備考</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="attendance in attendanceRecords">
                <td scope="row"></td>
                <td style="padding-top: 15px">{{ attendance.on_duty }}</td>
                <td v-if="attendance.created_at === attendance.updated_at" style="padding-top: 15px">{{ attendance.time }}</td>
                <td v-if="attendance.created_at !== attendance.updated_at" style="padding-top: 15px; color: red">{{ attendance.time }}</td>
                <td style="padding-top: 15px">{{ attendance.note }}</td>
                <td style="text-align: right">
                  <button @click="goEdit(attendance.id, attendance.time, attendance.on_duty, attendance.note)" type="button" class="btn btn-outline-primary"  data-toggle="modal" data-target="#exampleModal">編集</button>
                </td>
              </tr>
            </tbody>
          </table>

          <!--modal-->
          <div v-model="judgeUpdate" class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">勤怠履歴を編集する</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <h4>{{ label }}</h4>
                  <div class="form-group has-error" v-if="error !== ''">
                    <div class="alert alert-danger text-center">
                      <div>{{error}}</div>
                    </div>
                  </div>
                  <li style="list-style: none">日時:</li>
                  <v-date-picker v-model="date" mode="dateTime" is24hr>
                    <template v-slot="{ inputValue, inputEvents }">
                      <input
                        class="px-2 py-1 border rounded focus:outline-none focus:border-blue-300"
                        :value="inputValue"
                        v-on="inputEvents"
                      />
                    </template>
                  </v-date-picker>
                  <li style="list-style: none">備考:</li>
                  <input v-model="editNote" name="note" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" />
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
                  <button v-if="updateButton" @click="timeEdit()" type="button" class="btn btn-primary" data-dismiss="modal">更新する</button>
                  <button v-if="!updateButton" type="button" class="btn btn-primary" disabled>更新する</button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-3"></div>
      </div>
    </div>
    <div class="container">
      <div class="row">
        <div class="col-lg-3"></div>
        <div class="col-lg-6">
          <ul class="list-group">
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <td>通常勤務時間</td>
              <td>{{time}}</td>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <td>時間外</td>
              <td>{{overtime}}</td>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <td>深夜時間</td>
              <td>{{midnightTime}}</td>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <td>深夜時間外</td>
              <td>{{midnightOvertime}}</td>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <td>休憩時間</td>
              <td>{{restTime}}</td>
            </li>
          </ul>
        </div>
        <div class="col-lg-3"></div>
      </div>
    </div>
    <div style="text-align:center; padding-top: 20px;" class="homeButton">
      <button @click.stop.prevent="goHome()" type="button" class="btn btn-outline-info">ホームへ戻る</button>
    </div>
  </div>
</template>

<script>
import Vue from 'vue';
import VCalendar from 'v-calendar';
Vue.use(VCalendar);

export default {
  data() {
    return {
      detailDate:'',
      detailDay:'',
      weeks:["日","月","火","水","木","金","土"],
      attendanceRecords:[],
      attendanceId: 0,
      time: '',
      overtime: '',
      midnightTime: '',
      midnightOvertime: '',
      restTime: '',
      label: '',
      editNote: '',
      from_day: '',
      to_day: '',
      updateButton: false,
      count: 0,
      theTime: '',
      date: new Date(),
      dateConf: new Date(0),
      error: '',
    };
  },
  mounted() {
    if (this.$route.params.theDay == null) {
      this.$router.go(-1);
    }
    this.calcAttendance();
  },
  computed: {
    judgeUpdate() {
      if (Date.parse(this.date) == Date.parse(this.dateConf)) {
        if (this.date != this.dateConf) {
          this.error = '時刻を編集してください。';
        }
      } else if (Date.parse(this.date) < Date.parse(this.to_day) && Date.parse(this.date) > Date.parse(this.from_day)) {
        this.updateButton = true;
        this.error = '';
      } else if (this.to_day == 'noData' && Date.parse(this.date) > Date.parse(this.from_day)) {
        this.updateButton = true;
        this.error = '';
      } else if (this.from_day == 'noData' && Date.parse(this.date) < Date.parse(this.to_day)) {
        this.updateButton = true;
        this.error = '';
      }else {
        this.updateButton = false;
        if (this.count == 0) {
          this.count = 1;
        } else {
          var authId = document.querySelector("meta[name='user-id']").getAttribute('content');
          axios.get('/api/from_day/' + authId + '/' + this.theTime)
          .then(res =>  {
            this.from_day = res.data;
            axios.get('/api/to_day/' + authId + '/' + this.theTime)
            .then(res =>  {
              this.to_day = res.data;
              if(this.from_day == 'noData' && Date.parse(this.date) > Date.parse(this.to_day)) {
                this.error = this.to_day + 'までで時間を編集してください。';
              } else if (this.to_day == 'noData' && Date.parse(this.date) < Date.parse(this.from_day)) {
                this.error = this.from_day + '以降で時間を編集してください。';
              } else if (this.from_day != 'noData' && this.to_day != 'noData') {
                if (Date.parse(this.date) < Date.parse(this.from_day) || Date.parse(this.date) > Date.parse(this.to_day)) {
                  this.error = this.from_day + 'と' + this.to_day + 'との間で時間を編集してください。';
                }
              }
            }).catch( error => { console.log(error); })
          }).catch( error => { console.log(error); })
        }
      }
    },
  },
  methods: {
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
    goEdit(id, time, on_duty, note) {
      this.error = '';
      this.attendanceId = id;
      this.date = time;
      this.dateConf = time;
      this.theTime = time;
      this.label = on_duty;
      this.editNote = note;
    },
    timeEdit() {
      var monthEx = ("0"+(this.date.getMonth() + 1)).slice(-2);
      var dateEx =  ("0"+this.date.getDate()).slice(-2);
      var hourEx =  ("0"+this.date.getHours()).slice(-2);
      var minitesEx =  ("0"+this.date.getMinutes()).slice(-2);
      var strDate = this.date.getFullYear() + '-' + monthEx + '-' + dateEx + ' ' + hourEx + ':' + minitesEx + ':00';
      const data = {
        time: strDate,
        note: this.editNote,
      }
      axios.post('/api/attendanceDetail/update/' + this.attendanceId, data)
      .then(res =>  {
      }).catch( error => { console.log(error); })
      this.calcAttendance();
    },
    calcAttendance() {
      const theDay = new Date(this.$route.params.theDay);
      const nextDay = new Date(theDay.getTime() + 86400000);
      const theMonth = theDay.getMonth() + 1;
      this.detailDate = theDay.getFullYear() + '/' + theMonth  + '/' + theDay.getDate();
      this.detailDay = this.weeks[theDay.getDay()];

      var authId = document.querySelector("meta[name='user-id']").getAttribute('content');
      var performances = [];
      const data = {
        day: theDay,
        to_day: nextDay,
      }
      console.log(theDay);
      console.log(nextDay);
      axios.post('/api/userAttendance/' + authId, data)
        .then(res =>  {
          console.log(res.data);
          if (res.data != 'noData') {
            performances = res.data;
          }

          this.attendanceRecords = [];
          performances.forEach((performance) => {
            if (this.attendanceRecords.length == 0 && performance.on_duty == '出勤') {
              this.attendanceRecords.push(performance);
            } else if (this.attendanceRecords.length != 0) {
              this.attendanceRecords.push(performance);
            }
          })

          var time = 0;
          var overtime = 0;
          var midnightTime = 0
          var midnightOvertime = 0;
          var restTime = 0;
          const hours8 = 28800000;
          const hours24 = 86400000;

          if(this.attendanceRecords.length != 0) {
            for (let x = 1; x < this.attendanceRecords.length; x++) {
              var pastData = Date.parse(this.attendanceRecords[x - 1].time);
              var nowData = Date.parse(this.attendanceRecords[x].time);
              var am5 = new Date(theDay.getFullYear(), theDay.getMonth(), theDay.getDate(), 5).getTime();
              var pm10 = new Date(theDay.getFullYear(), theDay.getMonth(), theDay.getDate(), 22).getTime();
              var loopTime = Math.floor((nowData - theDay.getTime()) / (24 * 60 * 60 * 1000));

              if (this.attendanceRecords[x - 1].on_duty == '出勤' || this.attendanceRecords[x - 1].on_duty == '休出') {
                var resultArray = this.detailCalc(theDay, time, overtime, midnightTime, midnightOvertime, hours8, hours24, pastData, nowData, am5, pm10,loopTime);
                time = resultArray[0];
                overtime = resultArray[1];
                midnightTime = resultArray[2];
                midnightOvertime = resultArray[3];
              } else if (this.attendanceRecords[x - 1].on_duty == '休入') {
                restTime += nowData - pastData;
              }
            }
          }

          this.time = this.convertTime(Math.floor(time / 1000));
          this.overtime = this.convertTime(Math.floor(overtime / 1000));
          this.midnightTime = this.convertTime(Math.floor(midnightTime / 1000));
          this.midnightOvertime = this.convertTime(Math.floor(midnightOvertime / 1000));
          this.restTime = this.convertTime(Math.floor(restTime / 1000));

        }).catch( error => { console.log(error); })
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
              if (y == loopTime) {
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