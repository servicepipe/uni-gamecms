(function($) {
	var Roulette = function(options) {
		var defaultSettings = {
			maxPlayCount : null, // x >= 0 or null
			speed : 9, // x > 0
			stopImageNumber : null, // x >= 0 or null or -1
			rollCount : 0, // x >= 0
			duration : 10, //(x second)	
			stopCallback : function() {
			},
			startCallback : function() {
			}
		}
		var defaultProperty = {
			playCount : 0,
			$rouletteTarget : null,
			imageCount : null,
			$images : null,
			originalStopImageNumber : null,
			totalWidth : null,
			leftPosition : 0,
			leftPositionStartPoint : 0,
			soundPlayPoint : 95,
			case_width : 190,

			maxDistance : null,
			slowDownStartDistance : 0,

			isRunUp : true,
			isSlowdown : false,
			isStop : false,

			distance : 0,
			stopDistance : 0,
			runUpDistance : 10000,
			isIE : navigator.userAgent.toLowerCase().indexOf('msie') > -1
		};
		var p = $.extend({}, defaultSettings, options, defaultProperty);

		var reset = function() {
			p.maxDistance = defaultProperty.maxDistance;
			p.slowDownStartDistance = defaultProperty.slowDownStartDistance;
			p.distance = defaultProperty.distance;
			p.isRunUp = defaultProperty.isRunUp;
			p.isSlowdown = defaultProperty.isSlowdown;
			p.isStop = defaultProperty.isStop;
			p.leftPositionStartPoint = p.leftPosition;
			p.stopDistance = p.stopDistance;
		}

		var randomInteger = function(min, max) {
			var rand = min - 0.5 + Math.random() * (max - min + 1)
			rand = Math.round(rand);
			return rand;
		}
		
		var slowDownSetup = function() {
			if(p.isSlowdown){
				return;
			}
			p.isSlowdown = true;
			p.slowDownStartDistance = p.distance;
			p.maxDistance = p.distance + (4*p.totalWidth);
			p.maxDistance += p.itemWidth - p.leftPosition % p.itemWidth;
			if (p.stopImageNumber != null) {
				_rand_distance = -p.case_width/2 + randomInteger(30, p.case_width - 30);
				p.maxDistance += (p.totalWidth - (p.maxDistance % p.totalWidth) + (p.stopImageNumber * p.itemWidth)) % p.totalWidth - p.leftPositionStartPoint + _rand_distance;
			}
		}

		var roll = function() {
			var speed_ = p.speed;

			if (p.isRunUp) {
				if (p.distance <= p.runUpDistance) {
					speed_ = (p.distance / p.runUpDistance) * p.speed;

					if(speed_ < 1) {
						speed_ = speed_ + 0.5;
					}
				} else {
					p.isRunUp = false;
				}
			} else if (p.isSlowdown) {
				if(p.stopDistance == 0) {
					p.stopDistance = p.maxDistance - p.distance;
				}
				speed_ = ( (p.maxDistance - p.distance) / p.stopDistance) * p.speed;

				if(speed_ < 1) {
					speed_ = 1;
				}
			}

			if (p.maxDistance && p.distance >= p.maxDistance) {
				p.isStop = true;
				reset();

				p.stopCallback(p.$rouletteTarget.find('div.subject-block').eq(p.stopImageNumber));
				return;
			}

			p.distance += speed_;
			p.leftPosition += speed_;

			if (p.leftPosition >= (p.totalWidth + p.case_width)) {
				p.leftPosition = p.leftPosition - p.totalWidth;
			}

			p.soundPlayPoint += speed_;
			if(p.soundPlayPoint >= (p.itemWidth)) {
				p.soundPlayPoint = p.soundPlayPoint - p.itemWidth;
				play_case_sound('scroll');
			}

			if (p.isIE) {
				p.$rouletteTarget.css('left', '-' + p.leftPosition + 'px');
			} else {
				p.$rouletteTarget.css('transform', 'translateX(-' + p.leftPosition + 'px)');
			}

			setTimeout(roll, 1);
		}

		var init = function($roulette) {
			defaultProperty.originalStopImageNumber = p.stopImageNumber;
			if (!p.$images) {
				p.$images = $roulette.find('div.subject-block').remove();
				p.imageCount = p.$images.length;
				p.itemWidth = p.case_width;
				p.totalWidth = p.imageCount * p.itemWidth;
				p.runUpDistance = 2 * p.itemWidth;
			}
			p.leftPositionStartPoint = p.case_width;
			p.leftPosition = p.case_width;
			p.stopDistance = defaultProperty.stopDistance;
			p.soundPlayPoint = defaultProperty.soundPlayPoint;
			$roulette.find('div').remove();
			p.$rouletteTarget = $('<div>').css({
				'transform' : 'translateX(-' + p.leftPosition + 'px)'
			}).attr('class',"roulette-inner");
			$roulette.append(p.$rouletteTarget);
			p.$rouletteTarget.append(p.$images);
			p.$rouletteTarget.append(p.$images.clone());
			$roulette.show();
		}

		var start = function() {
			p.playCount++;
			if (p.maxPlayCount && p.playCount > p.maxPlayCount) {
				return;
			}
			p.stopImageNumber = $.isNumeric(defaultProperty.originalStopImageNumber) && Number(defaultProperty.originalStopImageNumber) >= 0 ?
									Number(defaultProperty.originalStopImageNumber) : Math.floor(Math.random() * p.imageCount); 
			p.startCallback();

			roll();

			setTimeout(function(){
				slowDownSetup();
			}, p.duration * 1000);
		}

		var stop = function(option) {
			if (!p.isSlowdown) {
				if (option) {
					var stopImageNumber = Number(option.stopImageNumber);
					if (0 <= stopImageNumber && stopImageNumber <= (p.imageCount - 1)) {
						p.stopImageNumber = option.stopImageNumber;
					}
				}
				slowDownSetup();
			}
		}
		var option = function(options) {
			p = $.extend(p, options);
			p.speed = Number(p.speed);
			p.duration = Number(p.duration);
			p.duration = p.duration > 1 ? p.duration - 1 : 1; 
			defaultProperty.originalStopImageNumber = options.stopImageNumber; 
		}

		var ret = {
			start : start,
			stop : stop,
			init : init,
			option : option
		}
		return ret;
	}

	var pluginName = 'roulette';
	$.fn[pluginName] = function(method, options) {
		return this.each(function() {
			var self = $(this);
			var roulette = self.data('plugin_' + pluginName);

			if (roulette) {
				if (roulette[method]) {
					roulette[method](options);
				} else {
					console && console.error('Method ' + method + ' does not exist on jQuery.roulette');
				}
			} else {
				roulette = new Roulette(method);
				roulette.init(self, method);
				$(this).data('plugin_' + pluginName, roulette);
			}
		});
	}
})(jQuery);

if(get_cookie('roulette_sound') == 'off') {
	if($('#sound-point').length == 1) {
		$('#sound-point').addClass('sound-off');
	}
	cases_roulette_sound = 2;
} else {

	if($('#sound-point').length == 1) {
		$('#sound-point').addClass('sound-on');
	}
	cases_roulette_sound = 1;
}