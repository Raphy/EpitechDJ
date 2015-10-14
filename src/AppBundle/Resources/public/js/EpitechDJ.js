function EpitechDJ(params) {
    var defaultParams = {
        loadCurrentVideoUri: null,
        loadQueueUri: null,
        addVideoUri: null,
        currentVideoLoaded: null,
        queueLoaded: null,
        videoAdded: null,
        addVoteUri: null,
        votesUri: null,
        votesLoaded: null
    };
    for (param in defaultParams) {
        if (params.hasOwnProperty(param)) {
            this[param] = params[param];
        }
    }
    for (param in params) {
        if (params.hasOwnProperty(param)) {
            this[param] = params[param];
        }
    }

    this["loadCurrentVideoLock"] = false;
    this["loadQueueLock"] = false;
    this["currentVideo"] = null;
    this["lastQueueTimestamp"] = 0;
    this["queue"] = [];
    this["ready"] = false;
    this["loadVotesLock"] = false;
    this["votes"] = {};
}

EpitechDJ.prototype.isReady = function () {
    return this.ready;
};

EpitechDJ.prototype.getCurrentVideo = function () {
    return this.currentVideo
};

EpitechDJ.prototype.getQueue = function () {
    return this.queue;
};

EpitechDJ.prototype.getQueueDuration = function (login) {
    var seconds = 0;
    for (i = 0; i < this.queue.length; i++) {
        if (typeof login != "undefined" && this.queue[i].user.login == login)
            return seconds;
        seconds += parseInt(this.queue[i].youtube.length_seconds);
    }
    return seconds;
};

EpitechDJ.prototype.getVotes = function () {
    return this.votes;
}

EpitechDJ.prototype.addVideo = function (url) {
    if (this.addVideoUri != null) {
        // Requesting
        var self = this;
        $.post(this.addVideoUri, {url: url}).success(function (response) {
            // User callback
            if (typeof self.videoAdded == "function")
                self.videoAdded(response.type, response.message);
        });
    }
};

EpitechDJ.prototype.addVote = function (type) {
    if (this.addVoteUri != null) {
        // Requesting
        $.post(this.addVoteUri, {type: type});
    }
};

EpitechDJ.prototype.sync = function () {
    this.ready = true;
    setInterval((function (self) {
        return function () {
            // Loading the current video
            if (self.loadCurrentVideoLock == false && self.loadCurrentVideoUri != null) {
                // Locking
                self.loadCurrentVideoLock = true;

                // Requesting
                $.get(self.loadCurrentVideoUri).success(function (video) {
                    // Setting the current video if not exists or different of the current
                    if (self.currentVideo == null || self.currentVideo.id != video.id) {
                        self.currentVideo = video;

                        // User callback
                        if (typeof self.currentVideoLoaded == "function")
                            self.currentVideoLoaded(self.currentVideo);

                        // Forcing to re update the queue
                        self.lastQueueTimestamp = 0;
                    }

                    // Unlocking
                    self.loadCurrentVideoLock = false;
                }).error(function () {
                    self.loadCurrentVideoLock = false;
                });
            }

            // Loading the queue
            if (self.loadQueueLock == false && self.loadQueueUri != null) {
                // Locking
                self.loadQueueLock = true;

                // Requesting
                $.get(self.loadQueueUri + "?from=" + self.lastQueueTimestamp).success(function (videos) {
                    // Adding the videos into the queue array
                    if (self.lastQueueTimestamp == 0)
                        self.queue = [];
                    for (i = 0; i < videos.length; i++)
                        self.queue.push(videos[i]);

                    // User callback
                    if (typeof self.queueLoaded == "function" && (videos.length > 0 || self.lastQueueTimestamp == 0))
                        self.queueLoaded(self.lastQueueTimestamp == 0 ? self.queue : videos, self.lastQueueTimestamp == 0);

                    // Saving the timestamp of the last video to get the list from it
                    if (self.queue.length > 0)
                        self.lastQueueTimestamp = self.queue[self.queue.length - 1].timestamp;

                    // Unlocking
                    self.loadQueueLock = false;
                }).error(function () {
                    self.loadCurrentVideoLock = false;
                });
            }

            // Loading the votes
            if (self.loadVotesLock == false && self.votesUri != null) {
                // Locking
                self.loadVotesLock = true;

                // Requesting
                $.get(self.votesUri).success(function (votes) {
                    // User callback
                    if (typeof self.votesLoaded == "function" && JSON.stringify(self.votes) !== JSON.stringify(votes))
                        self.votesLoaded(votes.count, votes.volume, votes.heart, votes.repeat);
                    self.votes = votes;

                    // Unlocking
                    self.loadVotesLock = false;
                }).error(function () {
                    self.loadVotesLock = false;
                });
            }
        };
    })(this), 2000);
};