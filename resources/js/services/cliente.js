import Axios from "axios";

export default {
    cadastra: function (data) {
        return Axios.post('clientes', data);
    },

    lista: function (page) {
        return Axios.get('clientes?page=' + page);
    },

    atualiza: function (id, data) {
        return Axios.put('clientes/' + id, data);
    },

    apaga: function (id) {
        return Axios.delete('clientes/' + id);
    },

    numeroclientes: function () {
        return Axios.get('numero-clientes');
    },

}
