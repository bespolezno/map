<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bani Yas Travel</title>
    <meta name="viewport"
          content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0">
    <link rel="stylesheet"
          type="text/css"
          href="/assets/css/style.css">
    <script type="module"
            src="/assets/js/app.js"></script>
</head>
<body>
<div id="app">
    <div class="wrapper">
        <aside :class="{hidden: aside}"
               class="side">
            <div class="item action">
                <ul class="nav">
                    <li v-if="!isAuthorized">
                        <a href="#"
                           @click.prevent="toggleModal">Login</a>
                    </li>
                    <li v-else>
                        <a href="#"
                           @click.prevent="logout">Logout (@{{ username }})</a>
                    </li>
                    <li class="toggle"><a href="#" @click="aside = true">&lAarr;</a></li>
                </ul>
            </div>
            <div v-if="isAdmin"
                 class="item">
                <h2 class="heading"
                    style="color: #4CAF50">@{{ !editing ? "Create" : "Edit"}} place</h2>
                <div class="panel-content">
                    <form @submit.prevent="savePlace"
                          ref="place">
                        <div class="form-group">
                            <input type="text"
                                   :required="!editing"
                                   name="name"
                                   placeholder="Name: "
                                   class="input">
                        </div>
                        <div class="form-group">
                            <input type="text"
                                   :required="!editing"
                                   name="latitude"
                                   pattern="^-?\d+(\.\d+)?$"
                                   placeholder="Latitude: "
                                   class="input">
                        </div>
                        <div class="form-group">
                            <input type="text"
                                   :required="!editing"
                                   name="longitude"
                                   pattern="^-?\d+(\.\d+)?$"
                                   placeholder="Longitude: "
                                   class="input">
                        </div>
                        <div class="form-group">
                            <input type="file"
                                   :required="!editing"
                                   name="image"
                                   class="input">
                        </div>
                        <div class="form-group">
                            <textarea placeholder="Description: "
                                      name="description"
                                      class="input"
                                      style="resize: none"
                                      rows="4"></textarea>
                        </div>
                        <div class="form-group form-content-right">
                            <button v-if="editing"
                                    @click="editing = null"
                                    class="btn">Back
                            </button>
                            <button type="submit"
                                    class="btn success">Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="item form">
                <h1 role="heading"
                    class="heading">Bani Yas Travel</h1>
                <div class="panel-content">
                    <form role="form"
                          @submit.prevent="search">
                        <datalist id="places">
                            <option v-for="place in places"
                                    :value="place.id">@{{ place.name }}
                            </option>
                        </datalist>
                        <div class="form-group">
                            <input v-model="from"
                                   type="search"
                                   name="from"
                                   list="places"
                                   required
                                   placeholder="From: "
                                   class="input">
                        </div>
                        <div class="form-group">
                            <input v-model="to"
                                   type="search"
                                   name="target"
                                   list="places"
                                   required
                                   placeholder="Target: "
                                   class="input">
                        </div>
                        <div class="form-group">
                            <input v-model="time"
                                   type="time"
                                   placeholder="Departure time: "
                                   class="input">
                        </div>
                        <div class="form-group form-content-right">
                            <button type="submit"
                                    class="btn btn-submit">Get Routes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="item panel-list">
                <ul class="result-list">
                    <li v-for="schedule in schedules"
                        :class="{selected: schedule === selectedSchedule}"
                        @click="select(schedule)">
                        <ul class="result-detail">
                            <li>
                                <label>From: </label>
                                <p>@{{ fromPlace(schedule) }}</p>
                            </li>
                            <li>
                                <label>To: </label>
                                <p>@{{ toPlace(schedule) }}</p>
                            </li>
                            <li>
                                <label>Time Schedule: </label>
                                <p>@{{ timeSchedule(schedule) }}</p>
                            </li>
                            <ul v-if="schedule === selectedSchedule" class="route-list">
                                <li v-for="(path, i) in schedule" class="route">
                                    <b>@{{ path.from_place.name }} to @{{ path.to_place.name }}</b><br>
                                    departure time -> @{{ path.departure_time }}<br>
                                    arrival time -> @{{ path.arrival_time }}<br>
                                    @{{ path.type }} Line @{{ path.line }}, @{{ path.travel_time }} minutes, @{{ i }} transfers.</li>
                            </ul>
                        </ul>
                    </li>
                    <li v-if="schedules.length === 0">
                        Sorry, no routes found
                    </li>
                </ul>
            </div>
        </aside>
        <main class="map">
            <div class="map-container">
                <svg class="map-lines"
                     viewBox="0 0 1280 800"
                     @click="$bus.$emit('hideCard')">
                    <image href="./assets/map.svg"></image>
                    <line x1="30"
                          x2="135"
                          y1="30"
                          y2="30"
                          stroke-dasharray="0"
                          stroke="black"
                          stroke-width="2"></line>
                    <text x="140"
                          y="35"
                          font-size="16">Bus
                    </text>
                    <line x1="30"
                          x2="135"
                          y1="60"
                          y2="60"
                          stroke-dasharray="5"
                          stroke="black"
                          stroke-width="2"></line>
                    <text x="140"
                          y="65"
                          font-size="16">Train
                    </text>
                    <line v-for="line in selectedSchedule"
                          :x1="line.from_place.x"
                          :x2="line.to_place.x"
                          :y1="line.from_place.y"
                          :y2="line.to_place.y"
                          :stroke-dasharray="line.type === 'TRAIN' ? 5 : 0"
                          :stroke="lineColor(line)"
                          stroke-width="2"></line>
                    <template v-for="place in places">
                        <ellipse :cx="place.x"
                                 :cy="place.y"
                                 @click.stop="$bus.$emit('showCard', place)"
                                 :fill="placeColor(place)"
                                 rx="4"
                                 ry="4"></ellipse>
                        <text :x="place.x + 10"
                              :y="place.y + 10"
                              font-size="10">@{{ place.name }}
                        </text>
                    </template>
                </svg>
                <place-card></place-card>
            </div>
            <div @click="aside = false"
                 class="menu">&backcong;</div>
        </main>
        <modal :show="show">
            <template v-slot:header>
                <h2 class="heading">Login</h2>
            </template>
            <template>
                <form role="form"
                      @submit.prevent="login">
                    <div v-if="!!forms.login.message"
                         class="invalid-feedback">@{{ forms.login.message }}
                    </div>
                    <div class="form-group">
                        <input v-model="forms.login.username"
                               type="text"
                               placeholder="Username"
                               class="input">
                    </div>
                    <div class="form-group">
                        <input v-model="forms.login.password"
                               type="password"
                               placeholder="Password"
                               class="input">
                    </div>
                    <div class="form-group">
                        <button class="btn btn-login">Login</button>
                    </div>
                </form>
            </template>
            <template v-slot:footer>
                <div class="form-group form-content-right">
                    <button @click="toggleModal(false)"
                            class="btn danger">Close
                    </button>
                </div>
            </template>
        </modal>
    </div>
</div>
</body>
</html>
