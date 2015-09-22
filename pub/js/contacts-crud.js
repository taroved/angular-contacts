var app = angular.module('contactsApp')
    .controller('contactController', ['$scope', '$http', ContactController]);

function ContactController($scope, $http) {
    this.showList = true;
    var newContact = {name:'', email:''};
    this.contactList = [];
    c = this; // controller

    this.clickAdd = function() {
        this.showList = false;
        $scope.newContactMode = true;
        $scope.contact = angular.copy(newContact);
        $scope.form.$setUntouched();
        $scope.form.$submitted = false;
    };

    this.rowEdit = function(id) {
        $http.get('/api/contacts/' + id)
            .then(function(response) {
                $scope.contact = angular.copy(response.data);
                c.showList = false;
                $scope.newContactMode = false;
            }, function(response) {
                console.log(response);
            });
    };

    this.rowDelete = function(id) {
        if (confirm("Are you shure?"))
            $http.delete('/api/contacts/' + id)
                .then(function(response) {
                    c.updateContacts();
                    console.log(response);
                }, function(response) {
                    console.log(response);
                });
    };

    this.cancelEdit = function() {
        this.showList = true;
    };

    this.updateContacts = function() {
        $http.get('/api/contacts').
            then(function(response) {
                c.contactList = response.data;
            }, function(response) {
            console.log(response);
            });
    };

    $scope.update = function(contact) {
        if ($scope.form.$valid) {
            $http.put('/api/contacts', contact)
                .then(function(response) {
                    c.showList = true;
                    c.updateContacts();
                }, function(response) {
                    console.log(response);
                });
        }
        $scope.form.$submitted = true;
    };
    $scope.create = function(contact) {
        if ($scope.form.$valid) {
            $http.post('/api/contacts', contact)
                .then(function(response) {
                    c.showList = true;
                    c.updateContacts();
                }, function(response) {
                    console.log(response);
                });
        }
        $scope.form.$submitted = true;
    }
    this.updateContacts();
}

app.directive('contactsCrud', ContactsCrudDirective);

function ContactsCrudDirective() {
    return {
        restrict: 'E',
        templateUrl: 'contacts-crud.html'
    };
}
