<?php

namespace Application\Vk;

use Application\Vk\Model\Reply;

class Service
{
    protected $_accessToken = '999cbc5dadce83a8994da1bcba13c2b1c3b1c23b1c23b1c23b1c234cb4cbc1c676c107342d7a1b4698736';
    //protected $_groupId = 78459596; // test group
    protected $_groupId = 78224356;

    private function _executeMethod($_method, $_params)
    {
        $url = "https://api.vk.com/method/{$_method}?access_token={$this->_accessToken}&v=5.25";

        $opts = array('http' => array(
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => http_build_query($_params),
        ));

        $context  = stream_context_create($opts);
        $result = file_get_contents($url, false, $context);
        $jsonResult = json_decode($result);

        if (property_exists($jsonResult, 'response')) {
            return $jsonResult->response;
        } else {
            echo "ERROR in {$_method} : " . PHP_EOL;
            var_dump($result);
            echo '--------' . PHP_EOL;
        }
    }

    public function postMessage($_messageText)
    {
        $response = $this->_executeMethod('wall.post', array(
            'owner_id' => - $this->_groupId,
            'message' => $_messageText,
            'from_group' => 1
        ));

        return $response->post_id;
    }

    public function postReply($_postId, $_messageText)
    {
        $response = $this->_executeMethod('wall.addComment', array(
            'owner_id' => - $this->_groupId,
            'post_id' => $_postId,
            'text' => $_messageText,
            'from_group' => 1,
        ));
    }

    public function changeMessage($_messageId, $_messageText)
    {
        $response = $this->_executeMethod('wall.edit', array(
            'owner_id' => - $this->_groupId,
            'post_id' => $_messageId,
            'message' => $_messageText,
        ));
    }

    /**
     * @param $_messageId
     * @return \Application\Vk\Model\Reply[]
     */
    public function getReplies($_messageId)
    {
        $comments = $this->_executeMethod('wall.getComments', array(
            'owner_id' => - $this->_groupId,
            'post_id' => $_messageId,
        ))->items;
        $replies = array();
        foreach ($comments as $comment) {
            $replies[] = new Reply($comment->id, $comment->from_id, $comment->text);
        }

        return $replies;
    }

}