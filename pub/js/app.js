var app = angular.module('contactsApp', ['http-auth-interceptor'])
    .controller('contactController', ['$http', ContactController]);

function ContactController($http) {
    this.showList = true;
    this.contact = null;
    this.contactList = [];

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

app.controller('authController', ['$scope', '$http', AuthController]);

function AuthController($scope, $http) {
    this.showLogin = true;
    $scope.loginUser = {name:"", password:""};

    $scope.$on('event:auth-loginRequired', function(event, data){
        $('#authModal').modal({backdrop: 'static'});
    });

    this.showRegistration = function() {
        this.showLogin = false;
    };

    this.showLoginForm = function() {
        this.showLogin = true;
    };

    this.runLogin = function(user) {
        $http({
            method: 'POST',
            url: '/api/login',
            headers: {

        })
    };
}
