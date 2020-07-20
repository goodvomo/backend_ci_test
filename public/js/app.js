var app = new Vue({
    el: '#app',
    data: {
        login: '',
        pass: '',
        post: false,
        invalidLogin: false,
        invalidPass: false,
        invalidSum: false,
        invalidCommentText: false,
        notEnoughLikes: false,
        posts: [],
        addSum: 0,
        amount: 0,
        likes: 0,
        commentText: '',
        packs: [
            {
                id: 1,
                price: 5
            },
            {
                id: 2,
                price: 20
            },
            {
                id: 3,
                price: 50
            },
        ],
    },
    computed: {
        test: function () {
            var data = [];
            return data;
        }
    },
    created() {
        var self = this
        axios
            .get('/main_page/get_all_posts')
            .then(function (response) {
                self.posts = response.data.posts;
            })
    },
    methods: {
        logout: function () {
            console.log('logout');
        },
        logIn: function () {
            var self = this;
            if (self.login === '') {
                self.invalidLogin = true
            } else if (self.pass === '') {
                self.invalidLogin = false
                self.invalidPass = true
            } else {
                //'login=' + self.login + '&password=' + self.pass
                // Только так отработало...в противном случае на бек или не получал post/get вообще
                // или получал полу пустой массив с ключем в виде json {login : someLogin, password : somePassword}
                // Разбираться было бы дольше, чем перевести в строку, учитывая, что утт только 2 пар-ра используются :)
                self.invalidLogin = false
                self.invalidPass = false
                axios.post('/main_page/login',
                    'login=' + self.login + '&password=' + self.pass,
                    {
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    })
                    .then(function (response) {
                        if (response.data.message != undefined) {
                            $('.error-message').html(response.data.message);
                        } else {
                            setTimeout(function () {
                                $('#loginModal').modal('hide');
                            }, 500);
                            window.location.reload();
                        }
                    })
            }
        },
        addComment: function (id) {
            var self = this;
            if (self.commentText === '') {
                self.invalidCommentText = true
            } else {
                self.invalidCommentText = false
                axios.get('/main_page/comment_post/' + id + '/' + self.commentText)
                    .then(function (response) {
                        self.post = response.data.post;
                    })
            }

        },
        fiilIn: function () {
            var self = this;
            if (self.addSum === 0) {
                self.invalidSum = true
            } else {
                self.invalidSum = false
                axios.post('/main_page/add_money',
                    'sum=' + self.addSum,
                    {
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    })
                    .then(function (response) {
                        setTimeout(function () {
                            $('#addModal').modal('hide');
                        }, 500);
                    })
            }
        },
        openPost: function (id) {
            var self = this;
            axios
                .get('/main_page/get_post/' + id)
                .then(function (response) {
                    self.post = response.data.post;
                    if (self.post) {
                        setTimeout(function () {
                            $('#postModal').modal('show');
                        }, 500);
                    }
                })
        },
        addLike: function (id, isPost = true) {
            var self = this;
            var type = isPost ? 'post' : 'comment';
            axios
                .get('/main_page/like/' + type + '/' + id)
                .then(function (response) {
                    if (response.data.type == 'errorLike') {
                        self.notEnoughLikes = true;
                    } else if (response.data.type == 'comment') {
                        $('.heart#' + response.data.entityId).html('<svg class="bi bi-heart-fill" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">\n' +
                            '                      <path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z" clip-rule="evenodd"/>\n' +
                            '                    </svg>');
                        $('.heart#' + response.data.entityId).append('<span> ' + response.data.likes + '</span>');
                    } else {
                        self.likes = response.data.likes;
                    }
                })

        },
        buyPack: function (id) {
            var self = this;
            axios.post('/main_page/buy_boosterpack',
                'id=' + id
            )
                .then(function (response) {
                    self.amount = response.data.amount
                    if (self.amount !== 0) {
                        setTimeout(function () {
                            $('#amountModal').modal('show');
                        }, 500);
                    }
                })
        }
    }
});

