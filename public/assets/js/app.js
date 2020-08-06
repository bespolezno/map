"use strict";

import Vue from "./vue.esm.js";
import Modal from "./components/modal.js";
import api from "./api.js";
import broadcast from "./broadcast.js";
import PlaceCard from "./components/placeCard.js";

const debug = true;

Vue.config.productionTip = debug;
Vue.config.devtools = debug;
Vue.config.silent = !debug;

Vue.prototype.$bus = broadcast;
Vue.prototype.$token = null;
Vue.prototype.$api = api;

window.app = new Vue({
    el: "#app",
    components: {
        Modal,
        PlaceCard
    },
    data: {
        show: false,
        places: [],
        token: null,
        role: null,
        username: null,
        forms: {
            login: {
                username: "",
                password: "",
                message: ""
            }
        },
        from: "",
        to: "",
        time: "",
        schedules: [],
        selectedSchedule: [],
        editing: null,
        aside: false
    },
    methods: {
        toggleModal(show = !this.show) {
            this.show = show;
        },
        fromPlace(schedule) {
            return schedule[0].from_place.name || "";
        },
        toPlace(schedule) {
            return schedule[schedule.length - 1].to_place.name || "";
        },
        timeSchedule(schedule) {
            return schedule[0].departure_time || "00:00";
        },
        placeColor(place) {
            const {from, to} = this;
            return [+from, +to].includes(place.id) ? "red" : "black";
        },
        lineColor(line) {
            const colors = [
                "#4CAF50",
                "#e91e63",
                "#9c27b0",
                "#3F51B5",
                "#FF9800",
                "#009688",
                "#ffeb3b",
                "#FF5722",
                "#607d8b",
                "#795548",
                "#8bc34a",
                "#00bcd4",
            ];
            return colors[line.line - 1 % colors.length] || "black";
        },
        clearLS() {
            localStorage.clear();
        },
        saveLS() {
            localStorage.setItem('token', this.token);
            localStorage.setItem('role', this.role);
            localStorage.setItem('username', this.username);
        },
        loadLS() {
            this.token = localStorage.getItem('token');
            this.role = localStorage.getItem('role');
            this.username = localStorage.getItem('username');
        },
        async select(schedule) {
            if (this.selectedSchedule === schedule) return;

            this.selectedSchedule = schedule;
            const data = {
                from_place_id: schedule[0].from_place.id,
                to_place_id: schedule[schedule.length - 1].to_place.id,
                schedule_id: schedule.map(el => el.from_place.id)
            };
            const {json} = await this.$api(`route/selection?token=${this.token}`, 'POST', data);
        },
        async login() {
            const data = this.forms.login;
            const {code, json} = await this.$api(`auth/login`, 'POST', data);

            if (code === 401) {
                this.forms.login.message = json.message;
                return;
            }

            this.username = this.forms.login.username;

            this.forms.login = Object.fromEntries(
                Object
                    .keys(this.forms.login)
                    .map(el => [el, ""])
            );

            this.token = json.token;
            this.role = json.role;
            this.saveLS();
            this.loadPlaces();
            this.toggleModal(false);
        },
        async logout() {
            await this.$api(`auth/logout?token=${this.token}`);
            this.clearLS();
            this.token = null;
            this.role = null;
            this.points = [];
            this.schedules = [];
            this.selectedSchedule = [];
            this.toggleModal(true);
        },
        async loadPlaces() {
            const {code, json} = await this.$api(`place?token=${this.token}`);
            if (code === 200)
                this.places = json.sort((a, b) => a.name.localeCompare(b.name));
        },
        async deletePlace(place) {
            const {code} = await this.$api(`place/${place.id}?token=${this.token}`, 'DELETE');
            this.$bus.$emit('hideCard');
            if (code === 200)
                this.loadPlaces();
        },
        async editPlace(place) {
            this.editing = place;
            this.$bus.$emit('hideCard');
            for (const el of this.$refs.place.elements) {
                el.value = place[el.name] || "";
            }
        },
        async savePlace() {
            const data = new FormData(this.$refs.place);
            data.forEach(((value, key) => value ? "" : data.delete(key)));

            const edit = this.editing ? `/${this.editing.id}` : "";

            const {code} = await this.$api(`place${edit}?token=${this.token}`, 'POST', data);
            if (code === 422) return;

            this.$bus.$emit('hideCard');
            this.editing = null;
            this.$refs.place.reset();
            this.loadPlaces();
        },
        async search() {
            if (!this.isAuthorized) {
                this.toggleModal();
                return;
            }

            this.$bus.$emit('hideCard');
            this.selectedSchedule = null;

            const {from, to, time} = this;

            const {json} = await this.$api(`route/search/${from}/${to}${time ? "/" + time : ""}?token=${this.token}`);

            this.schedules = json.schedules.map(el => el.map(schedule => ({
                ...schedule,
                from_place: this.places.find(place => place.id === schedule.from_place.id),
                to_place: this.places.find(place => place.id === schedule.to_place.id)
            })));
        }
    },
    computed: {
        isAdmin() {
            return this.role === 'ADMIN';
        },
        isAuthorized() {
            return !!this.token;
        }
    },
    mounted() {
        this.$bus.$on('unauthorized', () => {
            this.toggleModal(true);
        });

        this.loadLS();
        this.loadPlaces();
    }
});