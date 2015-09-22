var app = angular.module('contactsApp', ['http-auth-interceptor']);

app.directive('userNameChars', UserNameCharsDirective);

function UserNameCharsDirective() {
    var isValid = function(s) {
        return /^[\da-zA-Z_]*$/.test(s);
    }

    return {
        restict: 'A',
        require: 'ngModel',
        link:function (scope, elm, attrs, ngModel) {
            ngModel.$parsers.unshift(function(value) {
                ngModel.$setValidity('invalidUserNameChars', isValid(value));
                return value;
            });
        }
    };
}

app.directive('compareTo', CompareToDirective);

function CompareToDirective() {
    return {
        restict: 'A',
        require: 'ngModel',
        scope: {
            otherModelValue: "=compareTo"
        },
        link:function (scope, elm, attrs, ngModel) {
            
            ngModel.$validators.compareTo = function(modelValue) {
                return modelValue == scope.otherModelValue;
            };

            scope.$watch("otherModelValue", function() {
                ngModel.$validate();
            });
        }
    };
}

app.directive('userNameAvailable', ['$http', '$location', UserNameAvailableDirective]);

function UserNameAvailableDirective($http, $location) {
    return {
        restict: 'A',
        require: 'ngModel',
        link:function (scope, elm, attrs, ngModel) {
            ngModel.$parsers.unshift(function(value) {
                if (!value)
                    ngModel.$setValidity('userNameAvailable', true);
                else
                    $http({
                        method: 'GET',
                        url: '/api/check_username',
                        params: {name: value}
                    })
                    .then(function(response) {
                        console.log(response.data.inuse);
                        ngModel.$setValidity('userNameAvailable', !response.data.inuse);
                    }, function(response){
                        console.log(response);
                    });
                return value;
            });
        }
    };
}


