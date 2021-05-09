<template>
    <div class="col-md-12">
        <p class="font-italic">Данная страница показывает количество сыгранных партий в игры из коллекции пользователя по месяцам</p>
        <div class="input-group mb-3">
            <input
                v-model="userName"
                ref="username"
                type="text"
                class="form-control"
                placeholder="Username пользователя с boardgamegeek.com"
                aria-label="Username пользователя с boardgamegeek.com"
                aria-describedby="basic-addon2"
            >
            <div class="input-group-append">
                <button
                    v-on:click="getPlays"
                    class="btn btn-outline-secondary"
                    type="button"
                    :disabled="loading"
                >
                    Показать
                </button>
            </div>
        </div>

        <div v-if="loading" class="d-flex justify-content-center m-4">
            <div class="spinner-border" role="status">
                <span class="sr-only">Загрузка...</span>
            </div>
        </div>

        <div v-if="userStats.headers.length > 0">
            <table
                class="table table-striped table-bordered table-hover table-sm table-responsive"
                style="font-size: .8em"
            >
                <thead>
                <tr>
                    <th scope="col" class="top" v-for="header in userStats.headers">{{ getTopHeader(header) }}</th>
                </tr>
                <tr>
                    <th scope="col" v-for="header in userStats.headers">{{ getMiddleHeader(header) }}</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(items) in userStats.items">
                    <td v-for="item in items">{{ item }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import api from "../api";

export default {
    data() {
        return {
            loading: false,
            userName: '',
            userStats: {
                headers: [],
                items: [],
            }
        };
    },

    mounted() {
        this.focusInput();
    },

    methods: {
        getTopHeader(header) {
            return header === '' ? '' : header[0];
        },

        getMiddleHeader(header) {
            return header === '' ? '' : header[1];
        },

        getPlays() {
            if (this.loading) {
                return;
            }

            if (!this.userName) {
                alert('Введите имя пользователя');
                return;
            }

            this.loading = true;
            this.userStats.headers = [];

            api.plays.get(this.userName)
                .then((response) => {
                    this.userStats = response.data.stats;
                    if (this.userStats.headers.length === 0) {
                        alert('Записей не найдено');
                    }
                })
                .catch((err) => alert(err.response.data.error))
                .finally(() => this.loading = false);
        },

        focusInput() {
            this.$refs.username.focus();
        }
    }
}
</script>

<style scoped>
    th {
        text-align: center;
        padding: 4px 2px;
    }

    th.top {
        writing-mode: vertical-rl;
        text-orientation: upright;
    }

    td {
        padding: 1px 2px;
    }
</style>
