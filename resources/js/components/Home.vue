<template>
  <div id="home">
    <header>
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            <h5>会社名：{{ group }}</h5>
          </div>
          <div class="col-lg-9">
            <p>ユーザー名：{{ username }}</p>
          </div>
          <div class="col-lg-3 login">
            <a href="#" class="">新規登録</a>
            <a href="#">ログイン</a>
            <a href="#">ログアウト</a>
            <svg @click="toggle()" width="2em" height="2em" viewBox="0 0 16 16" class="bi bi-list" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M2.5 11.5A.5.5 0 0 1 3 11h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4A.5.5 0 0 1 3 7h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4A.5.5 0 0 1 3 3h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
            </svg>
          </div>
        </div>
      </div>
    </header>

    <div class="container">
      <div class="row">
        <div class="col-lg-4"></div>
        <div class="col-lg-4 centerButton" div v-if="type === 'A'">
          <button @click="changeB(), taskOpen(), start()" type="button" class="btn btn-primary btn-lg">出勤</button>
        </div>
        <div class="col-lg-4 centerButton" div v-if="type === 'B'">
          <button @click="changeC(), stop(), reset(), start(), restName(), taskOpen()" type="button" class="btn btn-primary btn-lg">休入</button>
          <button @click="changeA(), stop(), reset(), emptytask(), taskOpen()" type="button" class="btn btn-primary btn-lg">退勤</button>
        </div>
        <div class="col-lg-4 centerButton" div v-if="type === 'C'">
          <button @click="changeB(), stop(), reset(), start(), emptytask(), taskOpen()" type="button" class="btn btn-primary btn-lg">休出</button>
        </div>
        <div class="col-lg-4"></div>

        <div class="col-lg-3"></div>
        <div class="col-lg-6">
          <div v-if="isTask" class="row">
            <div class="col-lg-8">
              <select id="select-box1" class="form-select" aria-label="Default select example" v-model="selected">
                <option :value="null" disabled>タスクを選択してください</option>
                <option v-for="task in tasks">{{ task }}</option>
              </select>
            </div>
            <div class="col-lg-4 centerButton">
              <button @click="taskName(), stop(), reset(), start(), test()" type="button" class="btn btn-primary">タスク切替</button>
              <button @click="test()" type="button" class="btn btn-primary">テスト</button>
            </div>
          </div>
        </div>
        <div class="col-lg-3"></div>

        <div class="col-lg-4"></div>
        <div class="col-lg-4">
          <div class="row">
            <div class="col-lg-2"></div>
            <div class="col-lg-5">
              <p>{{ taskLabel }}</p>
            </div>
            <div class="col-lg-3">
              <p v-if="isTime">{{formattedElapsedTime}}</p>
            </div>
            <div class="col-lg-2"></div>
          </div>
        </div>
        <div class="col-lg-4"></div>

      </div>
    </div>
    
    <div class="ready">
      <img class="img-fluid" src="https://picsum.photos/1550/600">
      <transition name="fade">
        <div v-if="isActive" class="col-lg-1 menu">
          <p @click="goSchedule()">勤務予定表</p>
          <p>勤務実績表</p>
          <p>タスク実績表</p>
          <p>給与計算</p>
        </div>
      </transition>
    </div>
    <drawer @close="toggle" :align="align" :closeable="true" :mask-closable="true">
      <div v-if="open">
        <p @click="goSchedule()">勤務予定表</p>
        <p>勤務実績表</p>
        <p>タスク実績表</p>
        <p>給与計算</p>
      </div>
    </drawer>
  </div>
</template>

<script>
import Drawer from "vue-simple-drawer";
import vSelect from 'vue-select';

export default {
  components: {
    Drawer
  },
  data() {
    return {
      group: 'YJC',
      isActive: false,
      username: '浅田　剛明',
      open: false,
      align: 'right',
      type: 'A',
      isTask: false,
      selected: null,
      tasks: ['メールチェック', '朝礼', '作業', '会議', '研修', '外出', '離席', 'その他'],
      isTaskButton: true,
      currentTime: new Date(),
      taskLabel: '',
      elapsedTime: 0,
      timer: undefined,
      isTime: false
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
    goSchedule() {
      this.$router.push("/schedule");
    },
    active() {  
      this.isActive = !this.isActive  
    },
    toggle() {
      this.open = !this.open
    },
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
      this.taskLabel = this.selected;
      this.isTime = true;
    },
    restName() {
      this.taskLabel = '休憩';
      this.isTime = true;
    },
    emptytask() {
      this.taskLabel = '';
      this.isTime = false;
    },
    test() {
      document.getElementById('select-box1').selectedIndex = 0;
    },
  }
};
</script>