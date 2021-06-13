import moment from 'moment';
import './styles/timer.css';
var diffTime;
document.addEventListener('DOMContentLoaded', ()=> {
    var container = document.querySelector('#countdown');
    diffTime = container.dataset.remainingTime;
    console.log("inside"+diffTime);
    console.log("outside "+diffTime*1000);

    var duration = moment.duration(diffTime*1000, 'milliseconds');
    var interval = 1000;
    setInterval(function(){
        duration = moment.duration(duration - interval, 'milliseconds');
        if(duration.days()!=0){
            document.getElementById("days").innerHTML = duration.days();
        }else{
            document.getElementById("days").parentElement.classList.add('hidden');
        }
        document.getElementById("hours").innerHTML = duration.hours();
        document.getElementById("minutes").innerHTML = duration.minutes();
        document.getElementById("seconds").innerHTML = duration.seconds();

    }, interval);

});
