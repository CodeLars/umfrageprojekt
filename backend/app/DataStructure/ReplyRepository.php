<?php

    namespace CML\DataStructure;

    use CML\Classes\DB;

    class ReplyRepository extends DB
    {

        private DB $dbConn;

        public function __construct()
        {
            parent::__construct();
            $this->dbConn = new DB();
        }

        public function getReplys(): array
        {
            $dbResult = $this->dbConn->sql2array("SELECT * FROM Reply");
            /* @var $surveys Survey[] */
            $answerOptions = [];
            foreach ($dbResult as $row) {
                $reply = new Reply();
                $reply->hydrateFromDBRow($row);
                $replys[] = $reply;
            }
            return $replys;
        }

        public function getReplyByID($id)
        {
            $result = $this->dbConn->sql2array("SELECT * FROM Reply WHERE id = ?", [$id]);
            if (empty($result)) {
                return null;
            }
            $answerOptions = new AnswerOption();
            $answerOptions->hydrateFromDBRow($result[0]);
            return $answerOptions;
        }

        /**
         * Fetch question answers.
         *
         * This method fetches the replys for a Comment from the database.
         *
         * @param int $commentID The ID of the comment to fetch the answers for.
         *
         * @return Replys[] The array of replys for the Comment.
         *
         * @throws \Exception
         */
        public function fetchCommentReplys(int $commentID): array
        {
            $stmt = <<<SQL
                SELECT * FROM Reply WHERE r_commentID = ?;
            SQL;
            try {
                $result = $this->dbConn->sql2array($stmt, [$commentID]);
                /* @var $replys reply[] */
                $replys = [];
                foreach ($result as $row) {
                    $reply = new Reply();
                    $reply->hydrateFromDBRow($row);
                    $replys[] = $reply;
                }
                return $replys;
            } catch (\Exception $e) {
                throw new \Exception("Error fetching Replys answers: " . $e->getMessage(), 500);
            }
        }

    /**
     * Create replys.
     *
     * This method creates reply entries in the DB for a Comment.
     *
     * @param Comment $comment The comment to create the reply for.
     * @throws \Exception
     */
    public function newReply(Comment $comment): void
    {
        try {

            foreach ($comment->replys as $reply) {
                $stmt = <<<SQL
                    INSERT INTO Reply (r_commentID, r_accountID, r_replyText, r_likeCount)
                    VALUES (?, ?, ?, ?)
                SQL;
                $result = $this->dbConn->sql2db($stmt, [
                    $comment->commentID,
                    $reply->accountID ?? "null",
                    $reply->replyText ?? "null",
                    $reply->likeCount ?? "null"
                ]);
                $reply->id = $result['insert_id'];
            }
        } catch (\Exception $e) {
            throw new \Exception("Error creating replys: " . $e->getMessage(), 500);
        }
    }
    }