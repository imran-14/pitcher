const express = require('express');
const router = express.Router();
const passport = require('passport');
const User = require('../models/user');
const jwt = require('jsonwebtoken');
const config = require('../config/database');
const storage = require('../config/storage');

// Check if username is available
router.get('/checkusername', function(req, res, next){
    const username = req.query.username;
    
    User.getUserByUsername(username, function(err, username){
        if(!username)
            return res.json({success: true, msg: 'Username available'});

        return res.json({success: false, msg: 'Username unavailable'});
    });
});

// Register user
router.post('/register', function(req, res){
     let newUser = {
        username: req.body.username,
        password: req.body.password,
        name: req.body.name,
        email: req.body.email,
        gender: req.body.gender,
        dob: req.body.dob
     }
    User.addUser(newUser, function(err, user){
        if(err)
            if(!user)
                return res.json({success: false, msg: 'User already exists'});
            else
                return res.json({success: false, msg: err});
        return res.json({success: true, msg: "User registered", properties: user})
    });

});

// Authenticate
router.post('/authenticate', function(req, res){

    const username = req.body.username;
    const password = req.body.password;
    User.getUserByUsername(username, function(err, user){
        if(err)
            return res.json({success: false, msg: err});
        if(!user)
            return res.json({success: false, msg: 'User not found'});

        User.comparePassword(password, user.password, function(err, isMatch){
            if(err) throw err;
            if(isMatch){
                const token = jwt.sign(user, config.secret, {
                    expiresIn: 604800 // 1 week
                });
                res.json({
                    success: true,
                    token: 'JWT ' + token,
                    user: {
                        name: user.name,
                        username: user.username,
                        email: user.email
                    }
                });
            } else {
                return res.json({success: false, msg: 'Wrong password'});
            }
        });
    });
});

// Profile
router.get('/profile', passport.authenticate('jwt', {session: false}) ,function(req, res, next){
    res.json({user: req.user});
});


// Search Users
router.get('/search/:myusername/:searchString', function(req, res, next){
    data = {
        username: req.params.myusername,
        searchString: req.params.searchString
    }
    User.searchUser(data, function(err, resdata){
        if(err)
           return res.json({success: false, msg: 'No results found'});
        else{
            return res.json({success: true, data: resdata});
        }
    });
});

// Send Request
router.get('/sendfollowrequest/:myusername/:friendusername', function(req, res, next){
    data = {
        username: req.params.myusername,
        friendusername: req.params.friendusername
    }
    User.sendFollowRequest(data, function(err, resdata){
        if(err)
           return res.json({success: false, msg: 'Could\'nt send request '});
        else{
            data = {
                relationship: resdata
            }
            return res.json({success: true, data: data});
        }
    });
});


// Notifications
router.get('/notifications/:username', function(req, res, next){
    var username = req.params.username;

	res.writeHead(200, {
		"Content-Type":"text/event-stream",
		"Cache-Control":"no-cache",
		"Access-Control-Allow-Origin": "*"
	});

    var interval = null;
    interval = setInterval(function(){
        User.Notification(username, function(err, resdata){
            console.log(resdata);
            res.write("retry: 2000\n");
            res.write("event: notification\n");
            res.write("data: " + JSON.stringify(resdata) + "\n\n");
        });
    },5000);
	
	req.connection.addListener("close", function () {
    	clearInterval(interval);
    }, false);
});


// Add follower
router.get('/addfollower/:username/:friendusername', function(req, res, next){
    data = {
        username: req.params.username,
        friendusername: req.params.friendusername
    }
    User.deleteFollowRequest(data, function(err, resdata){
        if(err)
           return res.json({success: false, msg: 'Could\'nt accept request '});
        else{
            console.log(resdata);
            User.addFollower(data, function(err, resdata){
                if(err)
                    return res.json({success: false, msg: 'Could\'nt accept request '});
                else
                    return res.json({success: true, data: "Successfully Accepted"});
            });
        }
    });
});

// Upload Images
router.post('/upload/img/profile', function(req, res, next){
    storage.Upload(req, res, function(err) {
         if (err) {
             return res.end({success: false, msg: "Something went wrong!"});
         }
         return res.json({success: true, msg: "File uploaded sucessfully!."});
     });
});

module.exports = router;