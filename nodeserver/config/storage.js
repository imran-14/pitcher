const multer = require('multer');

module.exports.Storage = multer.diskStorage({
    destination: function(req, file, callback){
        callback(null, "../tmp/uploads/img");
    },
    filename: function(req, file, callback){
        callback(null, file.fieldname + "_" + Date.now() + "_" + file.originalname);
    }
});

module.exports.Upload = multer({
    storage: this.Storage,
}).array("imgUploader", 3);