// データの管理
const state = {
  user: null
}

// stateから算出する値
const getters = {
  check: state => !! state.user, //確実に真偽値を返すために、二重否定
  username: state => state.user ? state.user.name : ''
}

//呼び出された「action」を元に、「state」を更新　※同期
const mutations = {
  setUser(state, user) {
    state.user = user
  }
}

//状態を変更するユーザーの操作やapiの呼び出しなど　※非同期
const actions = {
  async register(context, data) {
    // 会員登録 API を呼び出し、データを渡して、setUserミューテーションを実行
    const response = await axios.post('/api/register', data)
    context.commit('setUser', response.data)
  },
  async login(context, data) {
    const response = await axios.post('/api/login', data)
    context.commit('setUser', response.data)
  },
  async logout(context) {
    await axios.post('/api/logout')
    context.commit('setUser', null)
  },
  async currentUser (context) {
    const response = await axios.get('/api/user')
    const user = response.data || null //論理演算子
    context.commit('setUser', user)
  }
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
}