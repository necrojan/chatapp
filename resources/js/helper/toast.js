import Swal from 'sweetalert2';

export default {
    install (Vue, options) {
        Vue.prototype.$sweet = {
            toast: (position, button, timer) => {
                return Swal.mixin({
                    toast: true,
                    position: position,
                    showConfirmButton: button,
                    timer: timer
                })
            }
        }
    }
}