import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex);

import CFProConfig from './util/wpConfig'

const STATE = {
	loading: false,
	connected: false,
	forms: [
	],
	settings : {
		enhancedDelivery: false,
		generatePDFs: false
	},
	layouts : [
		{name:'FOFOF'}
	],
	account: {
		plan: String,
		id: Number,
		apiKeys: {
			public: String,
			secret: String,
			token: String
		}
	},
	strings: CFProConfig.strings
};


import { MUTATIONS } from './mutations';

import { ACTIONS } from './actions';

import  { GETTERS } from './getters';


import { accountSaver, formSaver } from './plugins';

const PLUGINS = [
	accountSaver,
	formSaver
];

const store =  new Vuex.Store({
  strict: false,
  plugins: PLUGINS,
  modules: {},
  state: STATE,
  getters: GETTERS,
  mutations: MUTATIONS,
  actions: ACTIONS
});


export default store;
