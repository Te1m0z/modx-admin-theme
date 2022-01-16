document.addEventListener('DOMContentLoaded', () => {
    

    let cx = window.innerWidth / 2,
        cy = window.innerHeight / 2,
        clientX,
        clientY

    document.querySelector('.moving-block').addEventListener('mousemove', (e) => {

        clientX = e.pageX,
        clientY = e.pageY
        
        request = requestAnimationFrame(updateMe)
    })

    document.querySelector('.moving-block').addEventListener('mouseleave', () => {
        gsap.to('.moving-block', 0.3, {
            transform: `rotate3d(0, 0, 0, 0deg)`
        })
    })

    function updateMe() {

        let dx = clientX - cx,
            dy = clientY - cy,
            tiltX = dy / cy,
            tiltY = dx / cx
            radius = Math.sqrt(Math.pow(tiltX, 2) + Math.pow(tiltY, 2))
            degree = radius * 50
        
        gsap.to('.moving-block', 0.3, {
            transform: `rotate3d(${tiltX}, ${tiltY}, 0, ${degree}deg)`
        })

        console.log(dx, dy)
    }
})