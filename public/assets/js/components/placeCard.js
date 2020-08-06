"use strict";

const PlaceCard = {
    data: () => ({
        card: {
            x: 0,
            y: 0,
            name: "",
            description: "",
            image_path: ""
        },
        show: false
    }),
    computed: {
        style() {
            const {x, y} = this.card;
            const left = (x + 5 + 300 > 1280) ? 1280 - 300 - 5 : x + 5;
            const top = (y + 5 + 350 > 800) ? 800 - 350 - 5 : y + 5;
            return {
                left: `${left}px`,
                top: `${top}px`
            };
        },
        src() {
            return `${location.href}/storage/place_images/${this.card.image_path}`;
        }
    },
    mounted() {
        this.$bus.$on('showCard', card => {
            this.card = card;
            this.show = true;
        });
        this.$bus.$on('hideCard', () => {
            this.show = false;
        });
    },
    // language=HTML
    template: `
        <transition name="fade">
            <div v-if="show"
                 :style="style"
                 :key="card.id"
                 class="card">
                <img class="card__image"
                     :src="src"
                     :alt="card.name">
                <div class="card__body">
                    <h3>{{ card.name }}</h3>
                    <p>{{ card.description }}</p>
                    <div v-if="$root.isAdmin"
                         class="card__footer">
                        <button @click="$root.deletePlace(card)"
                                class="btn danger">Delete place</button>
                        <button @click="$root.editPlace(card)"
                                class="btn success">Edit place</button>
                    </div>
                </div>
            </div>
        </transition>
    `
};

export default PlaceCard;
