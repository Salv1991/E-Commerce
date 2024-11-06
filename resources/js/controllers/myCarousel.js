import { Controller } from '@hotwired/stimulus'
export default class extends Controller {
   static targets = ['frame', 'item', 'progress', 'navigation', 'next', 'prev', 'dots']
   static classes = ['active', 'inactive']
   static values = {
       autoplay: {
           type: Boolean,
           default: false,
       },
       delay: {
           type: Number,
           default: 5000,
       },
       loop: {
           type: Boolean,
           default: true,
       },
       touchAngle: {
           type: Number,
           default: 45,
       },
       gap: {
           type: Number,
           default: 0,
       },
       touchRatio: {
           type: Number,
           default: 0.5,
       },
       swipeThreshold: {
           type: Number,
           default: 80,
       },
       infinite: {
           type: Boolean,
           default: false,
       },
       breakpoints: {
           type: Object,
           default: {},
       },
   }
   index = 0
   pages = 0
   indices = {}
   active = new Proxy(
       { page: 0 },
       {
           set: (obj, prop, value) => {
               if (prop == 'page') {
                   // If we click "prev" while on the first page
                   // then go to the last one.
                   if (value < 0) {
                       value = this.pages - 1
                   }
                   // If we click "next" while on the last page
                   // then go to the first one.
                   if (value >= this.pages) {
                       value = 0
                   }
                   this.slideTo(this.indices[value])
               }
               // Actually set the active.page prop to it's new value
               return Reflect.set(obj, prop, value)
           },
       },
   )
   dragging = false
   preventClick = false
   initialize() {
       this.resizeObserver = new ResizeObserver(() => this.slideTo(this.index, false))
   }
   connect() {
       this.element.addEventListener('editor:show', event => {
           this.slideTo(
               this.itemTargets.findIndex(
                   item => item == event.target.closest('[data-my-carousel-target="item"]'),
               ),
           )
       })
       this.element.addEventListener('editor:sticky', event => {
           const index = this.itemTargets.findIndex(
               item => item == event.target.closest('[data-my-carousel-target="item"]'),
           )
           this.autoplayValue = false
           if (this.hasProgressTarget) {
               this.progressTarget.style.visibility = 'hidden'
           }
           this.slideTo(index, false)
           document.addEventListener(
               'editor:reload',
               () => {
                   this.autoplayValue = true
                   this.slideTo(this.index, false)
               },
               { once: true },
           )
       })
       this.initNavigationTargets()
       if (this.maxIndex <= 0) {
           this.nextTarget.classList.add('hidden')
           this.prevTarget.classList.add('hidden')
       }
       this.calculateOrUpdateDots()
   }
   calculateOrUpdateDots() {
       const limit = this.countItems()
       const totalItems = this.itemTargets.length
       this.pages = Math.ceil(totalItems / limit)
       const remainingItems = totalItems % limit
       const lastPageItems = remainingItems > 0 ? limit - remainingItems + remainingItems : limit
       if (this.pages <= 1) {
           this.dotsTarget.innerHTML = ''
           if (this.hasPrevTarget) {
               this.prevTarget.classList.add('hidden')
           }
           if (this.hasNextTarget) {
               this.nextTarget.classList.add('hidden')
           }
           return
       }
       if (this.hasPrevTarget) {
           this.prevTarget.classList.remove('hidden')
       }
       if (this.hasNextTarget) {
           this.nextTarget.classList.remove('hidden')
       }
       let html = ''
       for (let i = 0; i < this.pages; i++) {
           let index = i * limit
           let itemsInPage = limit
           if (i === this.pages - 1) {
               itemsInPage = lastPageItems
               index = totalItems - itemsInPage
           }
           this.indices[i] = index
           html += `<button
               aria-label="Go to page ${i + 1}"
               data-my-carousel-target="navigation"
               data-action="my-carousel#navigate"
               data-index="${this.indices[i]}"
               data-page="${i}"
               class="
                   bg-gray-800
                   place-items-center
                   w-2
                   h-2
                   block
                   rounded-full
               ">
           </button>`
       }
       html = `<div class="flex justify-center items-center gap-2 p-2 rounded-full">` + html + `</div>`
       this.dotsTarget.innerHTML = html
   }
   initNavigationTargets() {
       this.navigationTargets
           .filter(element => typeof element.dataset.index)
           .forEach(element => {
               if (element.dataset.index > this.maxIndex || this.maxIndex <= 0) {
                   element.classList.add('hidden')
               }
           })
   }
   disconnect() {
       this.resizeObserver.disconnect()
   }
   itemTargetConnected(element) {
       this.resizeObserver.observe(element)
   }
   itemTargetDisconnected(element) {
       this.resizeObserver.unobserve(element)
       this.slideTo(Math.min(this.index, this.maxIndex), false)
   }
   prevent(event) {
       event.preventDefault()
   }
   startDragging(event) {
       if (!this.dragging && this.pages > 1) {
           let swipe = event.touches ? event.touches[0] : event
           clearTimeout(this._autoplay)
           if (this.hasProgressTarget) {
               this.progressTarget.style.visibility = 'hidden'
           }
           this.dragging = true
           this.frameTarget.classList.remove('transition-transform', 'my-carousel-transition')
           this.element.draggingX = parseInt(swipe.pageX)
           this.element.draggingY = parseInt(swipe.pageY)
       }
   }
   stopDragging(event) {
       if (this.dragging && this.pages > 1) {
           let swipe = event.touches ? event.touches[0] || event.changedTouches[0] : event
           let swipeDistance = swipe.pageX - this.element.draggingX
           let swipeDeg = (this.element.swipeSin * 180) / Math.PI
           let direction = 0
           if (Math.abs(swipeDistance) > this.swipeThresholdValue && swipeDeg < this.touchAngleValue) {
               swipeDistance > 0 ? direction-- : direction++
           }
           this.dragging = false
           if (direction < 0) {
               this.active.page--
           }
           if (direction > 0) {
               this.active.page++
           }
           setTimeout(() => (this.preventClick = false))
       }
   }
   drag(event) {
       if (this.dragging && this.pages > 1) {
           let swipe = event.touches ? event.touches[0] : event
           let subExSx = parseInt(swipe.pageX) - this.element.draggingX
           let subEySy = parseInt(swipe.pageY) - this.element.draggingY
           let powEX = Math.abs(subExSx << 2)
           let powEY = Math.abs(subEySy << 2)
           let swipeHypotenuse = Math.sqrt(powEX + powEY)
           let swipeCathetus = Math.sqrt(powEY)
           this.element.swipeSin = Math.asin(swipeCathetus / swipeHypotenuse)
           if ((this.element.swipeSin * 180) / Math.PI < this.touchAngleValue) {
               event.cancelable && event.preventDefault()
               event.stopPropagation()
               this.preventClick = true
               let offset = subExSx * parseFloat(this.touchRatioValue)
               this.frameTarget.style.transform = `translate3d(${this.translate + offset}px, 0px, 0px)`
           }
       }
   }
   click(event) {
       if (this.preventClick) {
           event.preventDefault()
           event.stopPropagation()
       }
   }
   slideTo(index, animating = true) {
       // Clear any pending autoplay interval.
       clearTimeout(this._autoplay)
       if (this.hasFrameTarget) {
           this.frameTarget.classList.toggle('transition-transform', animating)
           this.frameTarget.classList.toggle('my-carousel-transition', animating)
       }
       if (this.loopValue) {
           index = index > this.maxIndex ? 0 : index < 0 ? this.maxIndex : index
       } else {
           index = Math.max(0, index)
           index = Math.min(this.maxIndex, index)
       }
       this.index = index
       this.navigationTargets
           .filter(element => typeof element.dataset.index)
           .forEach(element => {
               if (element.dataset.index == this.index) {
                   element.classList.add(...this.activeClasses)
                   element.classList.remove(...this.inactiveClasses)
               } else {
                   element.classList.add(...this.inactiveClasses)
                   element.classList.remove(...this.activeClasses)
               }
           })
       if (this.hasFrameTarget) {
           this.frameTarget.style.transform = `translate3d(${this.translate}px, 0px, 0px)`
       }
       if (this.autoplayValue) {
           if (this.hasProgressTarget) {
               this.progressTarget.style.visibility = 'visible'
               this.progressTarget.style.animation = 'none'
               setTimeout(() => this.progressTarget.style.removeProperty('animation'))
           }
           if (this.itemTargets.length > this.countItems()) {
               this._autoplay = setTimeout(this.next.bind(this), this.delayValue)
           }
       }
   }
   navigate(event) {
       const page = parseInt(event.detail?.page ?? event.params?.page ?? event.currentTarget.dataset?.page)
       if (page !== this.active.page) {
           event?.preventDefault()
           event?.stopPropagation()
           event?.stopImmediatePropagation()
           this.active.page = page
       }
   }
   countItems() {
       let itemsCount = 1
       for (let breakpoint in this.breakpointsValue) {
           if (window.innerWidth < breakpoint) {
               break
           }
           itemsCount = this.breakpointsValue[breakpoint] // update number of items
       }
       return itemsCount
   }
   next(event) {
       event?.preventDefault()
       this.active.page++
   }
   prev(event) {
       event?.preventDefault()
       this.active.page--
   }
   get translate() {
       return -(this.index * (this.itemWidth + this.gapValue))
   }
   get frameWidth() {
       return this.frameTarget.getBoundingClientRect().width
   }
   get itemWidth() {
       return this.itemTarget.getBoundingClientRect().width
   }
   get maxIndex() {
       return this.itemTargets.length - 1
   }
}