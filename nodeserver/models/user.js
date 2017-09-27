const bcrypt = require('bcryptjs');
const database = require('../config/database');

// Register session
const session = database.getSession();

// Add new user
module.exports.addUser = function(newUser, callback){
    bcrypt.genSalt(10, function(err, salt){
        bcrypt.hash(newUser.password, salt, function(err, hash){
                newUser.password = hash;
                if(err) throw err;
                if(!session)
                    callback("Database error",null);
                else
                    // Store in the database
                    session
                        .run("CREATE (n:USER {username:{username},password:{password},name:{name},email:{email},gender:{gender},dob:{dob}}) RETURN n",newUser)
                        .subscribe({
                            onNext: function(record) {
                                callback(null, record._fields[0].properties);
                            },
                            onCompleted: function() {
                                // Completed
                                session.close();
                            },
                            onError: function(error) {
                                callback(error, null);
                            }
                        });
        });
    });
}

module.exports.getUserByUsername = function(username, callback){
    if(!session)
        callback("Database error",null);
    else{
        session
            .run("MATCH (n:USER {username:{username}}) RETURN n",{username:username})
            .then(function(result){
                if(result.records.length)
                    result.records.forEach(function(record) {
                        callback(null, record._fields[0].properties);
                    });
                else
                    callback("User doesn't exist", null);
                    
                // Completed!
                session.close();
            })
            .catch(function(error) {
                    callback(error,null);
                    console.log(error);
            });
    }
}

module.exports.searchUser = function(data, callback){
    if(!session)
        callback("Database error",null);
    else{
        session
            .run("MATCH (n:USERS) WHERE n.username <> '" + data.username +"' AND n.username STARTS WITH '" + data.searchString + "' OPTIONAL MATCH (user:USERS {username: '" + data.username +"'}) -[r]->(friend:USERS {username: n.username}) RETURN n, r")
            .then(function(result){
                if(result.records.length){
                    resData = [];
                    result.records.forEach(function(record, index) {
                        resData[index] = record._fields[0].properties;
                        console.log(record._fields[1]);
                        if(record._fields[1]){
                            resData[index].relationship = record._fields[1].type;
                        }else{
                            resData[index].relationship = null;
                        }
                    });
                    callback(null, resData);
                }
                else
                    callback("User doesn't exist", null);
                session.close();
                    
            })
            .catch(function(error) {
                callback(error,null);
            });
    }
}

module.exports.sendFollowRequest = function(data, callback){
    if(!session)
        callback("Database error",null);
    else{
        session
            .run("MATCH (user {username:'" + data.username + "'}), (friend {username:'" + data.friendusername + "'}) CREATE (user)-[r:HAS_REQUESTED]->(friend) RETURN r")
            .then(function(result){
                if(result.records[0]._fields[0].type == "HAS_REQUESTED")
                    callback(null, result.records[0]._fields[0].type);
                else
                    callback(err, null);
                session.close();
                    
            })
            .catch(function(error) {
                callback(error,null);
            });
    }
}

module.exports.Notification = function(username, callback){
    if(!session)
        callback("Database error",null);
    else{
        session
            .run("MATCH (friend:USERS)-[r:HAS_REQUESTED]->(user{username:'" + username + "'}) RETURN friend.username as username")
            .then(function(result){
                if(result.records.length){
                    resData = {};
                    result.records.forEach(function(record, index) {
                        resData[index] = record._fields[0];
                    });
                    callback(null, resData);
                }
                else
                    callback("Failed", null);
                session.close();           
            })
            .catch(function(error) {
                callback(error,null);
            });
    }
}

module.exports.deleteFollowRequest = function(data, callback){
    if(!session)
        callback("Database error",null);
    else{
        session
            .run("MATCH (user {username:'" + data.username + "'})<-[r:HAS_REQUESTED]-(friend {username:'" + data.friendusername + "'}) DELETE r")
            .then(function(result){
                if(result)
                    callback(null, result);
                else
                    callback(err, null);
                    
            })
            .catch(function(error) {
                callback(error,null);
            });
    }
}

module.exports.addFollower = function(data, callback){
    if(!session)
        callback("Database error",null);
    else{
        session
            .run("MATCH (user {username:'" + data.username + "'}),(friend {username:'" + data.friendusername + "'}) CREATE (user)<-[r:FRIENDSHIP]-(friend) RETURN r")
            .then(function(result){
                console.log(result);
                if(result)
                    callback(null, result);
                else
                    callback(err, null);
                    
            })
            .catch(function(error) {
                callback(error,null);
            });
    }
}

module.exports.comparePassword = function(candidatePassword, hash, callback){
    bcrypt.compare(candidatePassword, hash, function(err, isMatch){
        if(err) throw err;
        callback(null, isMatch);
    });
}