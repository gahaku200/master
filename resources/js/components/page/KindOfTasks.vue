<template>
  <div>
    <p>KindOfTasks</p>
    <form　class="form-group" ref="observer" action="/kindOfTask" id="kindOfTask" method="post" tag="form">
      <input type="hidden" name="_token" :value="csrf" />
      <div class="input-group mb-3">
        <span class="input-group-text" id="inputGroup-sizing-default">追加するタスクの名前</span>
        <div name="kindOfTask">
          <input v-model="taskName" name="task" type="task" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" />
        </div>
        <button @click="createTask()" type="button" class="btn btn-primary">+</button>
      </div>
    </form>
    <div class="row">
      <div class="col-lg-3"></div>
      <div class="col-lg-6">
        <ul class="list-group">
          <li v-for="task in tasks" class="list-group-item d-flex justify-content-between align-items-center">
            <td>{{ task.taskName }}</td>
            <td>
              <button v-if="task.orderNum != tasks.length" @click="up(task.id)" type="button" class="btn btn-outline-primary">↓</button>
              <button v-if="task.orderNum != 1" @click="down(task.id)" type="button" class="btn btn-outline-primary">↑</button>
              <button @click="edit(task.id, task.taskName)" type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#exampleModal">編集する</button>
              <button @click="deleteT(task.id, task.taskName)" type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#exampleModal2">削除する</button>
            </td>
          </li>
        </ul>

        <!--modal-->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">タスクを編集する</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <input v-model="updateName" name="task" type="task" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" />
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
                <button @click="update()" type="button" class="btn btn-primary">編集する</button>
              </div>
            </div>
          </div>
        </div>

        <!--modal-->
        <div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">タスクを削除する</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                「{{ editName }}」を本当に削除しますか？
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
                <button @click="deleteTask()" type="button" class="btn btn-primary" data-dismiss="modal">削除する</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3"></div>
    </div>

    <button @click.stop.prevent="goHome()">戻る</button>
  </div>
</template>

<script>
export default {
  data() {
    return {
      tasks: [],
      taskName: '',
      updateName: '',
      editName: '',
      taskId: 0,
      csrf: document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content"),
    }
  },
  mounted() {
    var isAdmin = document.querySelector("meta[name='is-admin']").getAttribute('content');
    if (isAdmin != '1') {
      this.goHome();
    }
    var group_id = document.querySelector("meta[name='group-id']").getAttribute('content');

    axios.get('/api/getKindOfTasks/' + group_id)
      .then(res =>  {
        this.tasks = [];
        res.data.forEach((task) => {
          this.tasks.push({id:task.id, orderNum: task.orderNum, taskName: task.taskName});
        })
      }).catch( error => { console.log(error); })
   },
  methods: {
    goHome() {
      this.$router.push("/");
    },
    async createTask() {
      var group_id = document.querySelector("meta[name='group-id']").getAttribute('content');
      document.querySelector("#kindOfTask").submit();
    },
    edit(id, name) {
      this.taskId = id;
      this.updateName = name;
    },
    update() {
      const data = {
        name: this.updateName
      }
      axios.post('/api/kindOfTask/update/' + this.taskId, data)
      this.$router.go({path: this.$router.currentRoute.path, force: true})
    },
    deleteT(id, name) {
      this.taskId = id;
      this.editName = name;
    },
    deleteTask() {
      axios.post('/api/kindOfTask/delete/' + this.taskId)
      .then(res =>  {
        this.tasks = [];
        res.data.forEach((task) => {
          this.tasks.push({id:task.id, orderNum: task.orderNum, taskName: task.taskName});
        })
      })
      .catch( error => { console.log(error); });
      //this.$router.go({path: this.$router.currentRoute.path, force: true})
    },
    up(id) {
      this.taskId = id;
      axios.post('/api/kindOfTask/up/' + this.taskId)
      .then(res =>  {
        this.tasks = [];
        res.data.forEach((task) => {
          this.tasks.push({id:task.id, orderNum: task.orderNum, taskName: task.taskName});
        })
      })
      .catch( error => { console.log(error); });
    },
    down(id) {
      this.taskId = id;
      axios.post('/api/kindOfTask/down/' + this.taskId)
      .then(res =>  {
        this.tasks = [];
        res.data.forEach((task) => {
          this.tasks.push({id:task.id, orderNum: task.orderNum, taskName: task.taskName});
        })
      })
      .catch( error => { console.log(error); });
    },
  },
};
</script>
