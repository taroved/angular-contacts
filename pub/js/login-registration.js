var app = angular.module('contactsApp')

app.controller('authController', ['$scope', '$rootScope', '$http', 'Base64', 'authService', AuthController]);

function AuthController($scope, $rootScope, $http, Base64, authService) {
    var c = this; // controler
    $scope.showLogin = true;
    var initLoginUser = {name:"", password:""},
        initRegUser = {name:"", password:"", password2:""};

    $scope.$on('event:auth-loginRequired', function(event, data){
        $('#authModal').modal({backdrop: 'static'});
        c.showLoginForm();
    });

    $scope.$on('event:auth-loginConfirmed', function(event, user){
        $rootScope.isLoggedin = true;
        $rootScope.currentUserName = user.name;
        $('#authModal').modal('hide');
    });

    $rootScope.logout = function() {
        $rootScope.isLoggedin = false;
        delete $http.defaults.headers.common.Authorization; // remove authorization from requests
        $rootScope.$broadcast('event:auth-loginRequired');
    };

    this.showRegistration = function() {
        c.showLogin = false;
        $scope.regUser = angular.copy(initRegUser);
        $scope.regForm.$setUntouched();
        $scope.regForm.$submitted = false;
    };

    this.showLoginForm = function() {
        c.showLogin = true;
        $scope.loginUser = angular.copy(initLoginUser);
        $scope.loginForm.$setUntouched();
        $scope.loginForm.$submitted = false;
    };

    this.runLogin = function(user) {
        if ($scope.loginForm.$valid) {
            var authdata = Base64.encode(user.name + ':' + user.password);
            $http({
                method: 'POST',
                url: '/api/login',
                headers: {'Authorization': 'Basic ' + authdata},
                ignoreAuthModule: true // option for http-auth-interceptor
            })
            .then(function(){
                // add authorization details to all requests
                $http.defaults.headers.common.Authorization = 'Basic ' + authdata;
                $scope.loginForm.invalidPair = false;
                authService.loginConfirmed(user);  // the func will resend queue of failed requests
            }, function(response){ 
                $scope.loginForm.invalidPair = true;
                console.log(response);
            });
        }
        $scope.loginForm.$submitted = true;
    };

    this.register = function(user) {
        if ($scope.regForm.$valid) {
            var authdata = Base64.encode(user.name + ':' + user.password);
            $http({
                method: 'POST',
                url: '/api/register',
                ignoreAuthModule: true, // option for http-auth-interceptor
                data: user
            })
            .then(function(){
                // add authorization details to all requests
                $http.defaults.headers.common.Authorization = 'Basic ' + authdata;
                authService.loginConfirmed(user);  // the func will resend queue of failed requests
            }, function(response){
                console.log(response);
            });
        }
        $scope.regForm.$submitted = true;
    }

}

app.directive('loginRegistration', LoginRegistrationDirective);

function LoginRegistrationDirective() {
    return {
        restrict: 'E',
        templateUrl: 'login-registration.html'
    };
}
