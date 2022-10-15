function setActivity () {
  let token = $('#token').val();

  $.ajax({
    type: 'POST',
    url: '../modules_extra/activity_rewards/ajax/actions.php',
    data: 'phpaction=1&setActivity=1&token=' + token,

    success: function (html) {
      $('body').append(html);
    }
  });
}

document.addEventListener("DOMContentLoaded", setActivity);

function getRewardsBanner (container) {
  let token = $('#token').val();

  $.ajax({
    type: 'POST',
    url: '../modules_extra/activity_rewards/ajax/actions.php',
    data: 'phpaction=1&getRewardsBanner=1&token=' + token,

    success: function (html) {
      $(container).html(html);
    }
  });
}

function getRewardsWidget (container) {
  let token = $('#token').val();

  $.ajax({
    type: 'POST',
    url: '../modules_extra/activity_rewards/ajax/actions.php',
    data: 'phpaction=1&getRewardsWidget=1&token=' + token,

    success: function (html) {
      $(container).html(html);
    }
  });
}

function getRewardLine (rewardId) {
  let token = $('#token').val();
  let rewardType = $('#type' + rewardId).val();
  rewardType = encodeURIComponent(rewardType);

  $.ajax({
    type: 'POST',
    url: '../modules_extra/activity_rewards/ajax/actions.php',
    data: 'phpaction=1&getRewardLine=1&token=' + token + '&rewardType=' + rewardType + '&rewardId=' + rewardId,

    success: function (html) {
      $('#reward-line' + rewardId).html(html);
    }
  });
}

function getRewards () {
  let token = $('#token').val();

  $.ajax({
    type: 'POST',
    url: '../modules_extra/activity_rewards/ajax/actions.php',
    data: 'phpaction=1&getRewards=1&token=' + token,

    success: function (html) {
      $('#rewards').html(html);
    }
  });
}

function getActivityRewardsProgress(partNumber) {
  let token = $('#token').val();
  $.ajax({
    type: "POST",
    url: "../modules_extra/activity_rewards/ajax/actions.php",
    data: "phpaction=1&getActivityRewardsProgress=1&token=" + token + "&partNumber=" + partNumber,

    success: function (html) {
      if (partNumber === 'first') {
        $("#progress").html(html);
      } else {
        dell_block("loader" + partNumber);
        $("#progress").append(html);
      }
      $('[tooltip="yes"]').tooltip();
    }
  });
}

function getServicesReward (rewardId, type) {
  let token = $('#token').val();
  let server = $('#server' + rewardId ).val();
  server = encodeURIComponent(server);

  $.ajax({
    type: 'POST',
    url: '../modules_extra/activity_rewards/ajax/actions.php',
    data: 'phpaction=1&getServicesReward=1&token=' + token + '&server=' + server + '&type=' + type,

    success: function (html) {
      $('#service' + rewardId).html(html);

      setTimeout(function () {
        getTariffsReward(rewardId, type);
      }, 500);
    }
  });
}

function getTariffsReward (rewardId, type) {
  let token = $('#token').val();
  let service = $('#service' + rewardId).val();
  service = encodeURIComponent(service);

  $.ajax({
    type: 'POST',
    url: '../modules_extra/activity_rewards/ajax/actions.php',
    data: 'phpaction=1&getTariffsReward=1&token=' + token + '&service=' + service + '&type=' + type,

    success: function (html) {
      $('#tarif' + rewardId).html(html);
    }
  });
}

function getShopKeyServicesReward (rewardId) {
  let token = $('#token').val();
  let server = $('#server' + rewardId).val();
  server = encodeURIComponent(server);

  $.ajax({
    type: 'POST',
    url: '../modules_extra/activity_rewards/ajax/actions.php',
    data: 'phpaction=1&getShopKeyServicesReward=1&token=' + token + '&server=' + server,

    success: function (html) {
      $('#tarif' + rewardId).html(html);
    }
  });
}

function dellReward (id) {
  $('#reward' + id).remove();
}

function saveRewards () {
  NProgress.start();

  let data = {};
  data['saveRewards'] = '1';

  let rewards = $('#rewards').serialize();

  $.ajax({
    type: 'POST',
    url: '../modules_extra/activity_rewards/ajax/actions.php',
    data: create_material(data) + '&' + rewards,
    dataType: 'json',

    success: function (result) {
      NProgress.done();
      if (result.status === 1) {
        setTimeout(show_ok, 500);
        reset_page();
      } else {
        setTimeout(show_error, 500);
        if (result.reply !== undefined) {
          show_input_error(result.input, result.reply, null);
        }
      }
    }
  });
}

function saveActivityRewardsConfig() {
  NProgress.start();

  let data = {};
  data['saveActivityRewardsConfig'] = '1';
  data['isReIssue'] = $('#is-re-issue').val();
  data['isNeedMoneyActivity'] = $('#is-need-money-activity').val();
  data['amountOfMoney'] = $('#amount-of-money').val();

  $.ajax({
    type: 'POST',
    url: '../modules_extra/activity_rewards/ajax/actions.php',
    data: create_material(data),
    dataType: 'json',

    success: function (result) {
      NProgress.done();
      if (result.status === 1) {
        setTimeout(show_ok, 500);
      } else {
        setTimeout(show_error, 500);
        if (result.reply !== undefined) {
          show_input_error(result.input, result.reply, null);
        }
      }
    }
  });
}

function dellActivityRewards () {
  if (confirm('Вы уверены?')) {
    NProgress.start();
    let token = $('#token').val();
    $.ajax({
      type: 'POST',
      url: '../modules_extra/activity_rewards/ajax/actions.php',
      data: 'phpaction=1&dellActivityRewards=1&token=' + token,

      success: function () {
        getActivityRewardsProgress('first');
        NProgress.done();
      }
    });
  }
}