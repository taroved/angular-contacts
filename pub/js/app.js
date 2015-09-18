var app = angular.module('contactsApp', [])
    .controller('contactController', ['$http', ContactController]);

function ContactController($http) {
    this.showList = true;
    this.contact = null;
    this.contactList = null;

    this.clickAdd = function() {
        this.showList = false;
        this.contact = {name:'', email:''};
    };

    this.cancelEdit = function() {
        this.showList = true;
    };

    var t = this;
    this.updateContacts = function() {
        $http.get('/api/contacts').
            then(function(response) {
                t.contactList = response.data;
            }, function(response) {
                console.log(response);
            });
    };
    this.updateContacts();
}
