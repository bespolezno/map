"use strict";

const Modal = {
    // language=HTML
    template: `
        <transition name="fade">
            <div v-if="show"
                 class="modal__bg">
                <div class="modal">
                    <div class="modal__header">
                        <slot name="header"></slot>
                    </div>
                    <div class="modal__body">
                        <slot></slot>
                    </div>
                    <div class="modal__footer">
                        <slot name="footer"></slot>
                    </div>
                </div>
            </div>
        </transition>
    `,
    props: {
        show: {
            default: false
        }
    }
};

export default Modal;