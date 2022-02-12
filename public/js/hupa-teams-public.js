
document.addEventListener("DOMContentLoaded", function (event) {

    window.addEventListener('resize', function (event) {
        let intViewportWidth = window.innerWidth;
        let teamBox = document.querySelectorAll('.template-one-free-cover-box');
        setTeamBoxOrder(teamBox, intViewportWidth)
    });

    let loadTeam = new Promise(function (resolve, reject) {
        let teamBox = document.querySelectorAll('.template-one-free-cover-box');
        resolve(teamBox);
        reject();
    });

    loadTeam.then(
        function (value) {
            let intViewportWidth = window.innerWidth;
            click_team_free_card();
            setTeamBoxOrder(value, intViewportWidth);
        },
        function (error) {
            console.log(error);
        }
    );

    function setTeamBoxOrder(values, width) {
        let i = 1;
        let x = 1;
        let o = 1;
        if (width > 1200) {
            x = 3;
        }
        if (width < 1200 && width > 992) {
            x = 2
        }
        if (width < 992) {
            x = 1
        }

        if (values) {
            let nodes = Array.prototype.slice.call(values, 0);
            nodes.forEach(function (nodes) {
                if (i == x) {
                    i = 0;
                    setCollapseOrderId(o, x);
                }
                i++;
                nodes.style.order = `${o}`;
                o++;
            })
        }

        function setCollapseOrderId(count, factor) {
            let setContainer = count + 1;
            let nextDetailId = document.querySelectorAll('.template-one-free-details');
            let start = count - factor;
            let x = 0;
            for (let i = start; i < count; i++) {
                let id = i + (x + 1) - 1;
                let container = nextDetailId[id];
                container.style.order = `${setContainer}`;
            }
        }
    }

    function click_team_free_card() {
       let templateOneFreeCardBody = document.querySelectorAll('.template-one-free-card-body');
       if(templateOneFreeCardBody){

           let node = Array.prototype.slice.call(templateOneFreeCardBody, 0);
           node.forEach(function (node) {
               node.addEventListener("click", function (e) {
                   let id = node.getAttribute('data-id');
                   let collParent = node.getAttribute('data-parent');
                   let searchOverviewColl = document.getElementById('teamDetails' + id);
                   let scrollActive = searchOverviewColl.getAttribute('data-scroll');
                   let scrollOffset = searchOverviewColl.getAttribute('data-scrolloffset');
                   let coverFree = document.querySelector('.coverFree'+id);
                   if(coverFree.classList.contains('active')){
                       coverFree.classList.remove('active');
                   } else {
                       for (let i = 0; i < templateOneFreeCardBody.length; i++) {
                           templateOneFreeCardBody[i].parentElement.parentNode.classList.remove('active')
                       }
                       coverFree.classList.add('active');
                       if(scrollActive){
                           scrollToContainer(searchOverviewColl, scrollOffset);
                       }

                   }
                   new bootstrap.Collapse(searchOverviewColl, {
                       toggle: true,
                       parent: collParent
                   });
               });
           });
       }
    }

    function scrollToContainer(target, offset) {
        setTimeout(function () {
            jQuery('html, body').animate({
                   scrollTop: jQuery(target).offset().top - (offset),
            }, 450, "linear", function () {
            });
        }, 1000);
    }
});