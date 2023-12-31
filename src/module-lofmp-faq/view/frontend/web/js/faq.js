define(['jquery'], function($)
{
    'use strict';
    return {
        showAnswer: function(questionId, speed) {
            var answerId = '#' + questionId.replace('title', 'answer');
            $(answerId).slideToggle(speed);
            $( '.answer' ).not(answerId).slideUp(speed);
        },

        closeOtherAnswers: function(categoryId, speed){
            $( '.answer' ).slideUp(speed);
            var questionListId = '#' + categoryId.replace('category-', 'question-list-');
            $('.questions-container').not(questionListId).slideUp(speed);
        }
    };
});
