export default {
    install (Vue, options) {
        Vue.prototype.$formatter = {
            dateFormat: (dateString, format) => {
                return moment(dateString).format(format ? format : 'MMMM DD, YYYY');
            },
            capitalizeText: (text) => {
                return text.charAt(0).toUpperCase() + text.slice(1);
            }
        }
    }
}