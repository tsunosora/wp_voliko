<template>
  <v-app>
    <div class="app-header">
        <h2>Hi {{userDisplayName}}, How are you today?</h2>
    </div>
    <div>
      <v-tabs hide-slider background-color="transparent" class="solution-tab-header">
        <v-tab
        class="solution-tab"
        v-for="(item, index) in contents"
        :key="index"
        :to="{path: item.link}">
          {{item.title}}
        </v-tab>
      </v-tabs>
      <transition :name="transitionName">
        <router-view></router-view>
      </transition>
    </div>
  </v-app>
</template>

<script>
export default {
  name: 'app',  
  data () {
    return {
      contents: [
        {
          title: "Modules",
          link: "/",
          text: "This is the first text",
        },
        {
          title: "Settings",
          link: "/settings",
          text: "This is the second text",
        }
      ],
      transitionName: 'slide-left',
      hambugerClass: 'hamburger hamburger--spin-r is-active',
      menuExpanded: true,
      userDisplayName: nb.user_display_name,
      hambugerWrapClass: 'menu-hambuger',
      appMenuClass: 'app-menu'
    }
  },
	// components: {
	//   Modules,
	//   Settings
	// },
	methods: {
		triggerMenu() {
		this.menuExpanded = !this.menuExpanded
		if(this.menuExpanded) {
			setTimeout(() => { this.hambugerWrapClass = 'menu-hambuger has-box-shadow' }, 250)
			setTimeout(() => { this.appMenuClass = 'app-menu has-box-shadow' }, 250)                
			this.hambugerClass= 'hamburger hamburger--spin-r is-active'
			// this.hambugerWrapClass = 'menu-hambuger has-box-shadow'
			// this.appMenuClass = 'app-menu has-box-shadow'
		} else {
			this.hambugerClass= 'hamburger hamburger--spin-r'
			this.hambugerWrapClass = 'menu-hambuger'
			this.appMenuClass = 'app-menu'
		}
		// if(this.menuExpanded) {
		//   this.menuIcon = 'clear'
		// }
		}
	}
	// beforeRouteUpdate (to, from, next) {
	//   const toDepth = to.path.split('/').length
	//   const fromDepth = from.path.split('/').length
	//   this.transitionName = toDepth < fromDepth ? 'slide-right' : 'slide-left'
	//   next()
	// },
}
</script>

<style lang="scss">
.nbt-solutions_page_solution-dashboard {
	#app {
		// margin-left: -180px;
		// padding-left: 180px;
		margin-top: -15px;
		padding-right: 20px;
		.card {
		margin-top: 0;
		}
	}
}

.hamburger {
	display: flex;
	cursor: pointer;
	transition-property: opacity, filter;
	transition-duration: 0.15s;
	transition-timing-function: linear;
	font: inherit;
	color: inherit;
	text-transform: none;
	background-color: transparent;
	border: 0;
	margin: 0;
	overflow: visible;
}

.hamburger:hover {
	opacity: 0.7;
}

.hamburger-box {
	width: 17px;
	height: 14px;
	display: inline-block;
	position: relative;
}

.hamburger-inner {
	display: block;
	top: 50%;
	margin-top: -2px;
}
	
.hamburger-inner, .hamburger-inner::before, .hamburger-inner::after {
	width: 17px;
	height: 2px;
	background-color: #fff;
	border-radius: 4px;
	position: absolute;
	transition-property: transform;
	transition-duration: 0.15s;
	transition-timing-function: ease;
}

	.hamburger-inner::before, .hamburger-inner::after {
		content: "";
		display: block; }
	.hamburger-inner::before {
		top: -5px; }
	.hamburger-inner::after {
		bottom: -5px; }

.hamburger--spin-r .hamburger-inner {
  transition-duration: 0.22s;
  transition-timing-function: cubic-bezier(0.55, 0.055, 0.675, 0.19); }
  .hamburger--spin-r .hamburger-inner::before {
    transition: top 0.1s 0.25s ease-in, opacity 0.1s ease-in; }
  .hamburger--spin-r .hamburger-inner::after {
    transition: bottom 0.1s 0.25s ease-in, transform 0.22s cubic-bezier(0.55, 0.055, 0.675, 0.19); }

.hamburger--spin-r.is-active .hamburger-inner {
  transform: rotate(-225deg);
  transition-delay: 0.12s;
  transition-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1); }
  .hamburger--spin-r.is-active .hamburger-inner::before {
    top: 0;
    opacity: 0;
    transition: top 0.1s ease-out, opacity 0.1s 0.12s ease-out; }
  .hamburger--spin-r.is-active .hamburger-inner::after {
    bottom: 0;
    transform: rotate(90deg);
    transition: bottom 0.1s ease-out, transform 0.22s 0.12s cubic-bezier(0.215, 0.61, 0.355, 1); }

#app {
  background: #F1F1F1;  
  a {
    text-decoration: none;
  }
}

.app-header {
  display: flex;
  justify-content: space-between;
  padding: 40px 10px 20px;
  > h2 {
    display: inline-block;
    font-size: 20px;
    color: #5b86e5;
    @media (max-width: 800px) {
      display: none;
    }
  }
  @media (max-width: 800px) {
    justify-content: flex-end;
  }
}

.app-menu {
  display: flex;
  align-items: stretch;
  height: 54px;
  border-radius: 27px;  
  a:focus {
    outline: 0;
    box-shadow: none;
  }
  // transition: all .5s ease-in-out;
  &.has-box-shadow {
    box-shadow: rgba(0,0,0,0.15) 0px 2px 5px 0px;
  }
  > div {
    display: inline-block
  }
  .menu-item {
    display: flex;
    align-items: center;
    background: #36d1dc;
    margin-right: -32px;
    padding-left: 30px;
    padding-right: 58px;
    border-top-left-radius: 27px;
    border-bottom-left-radius: 27px;
    min-width: 162px;
    justify-content: space-between;
    > a {
      &:before {
        top: 80%;
        background: #fff;
        color: #000;
        font-weight: bold;
      }
      &:after {
        bottom: -7px;
        left: 50%;
        border: solid transparent;
        content: " ";
        height: 0;
        width: 0;
        position: absolute;
        pointer-events: none;
        border-color: rgba(255, 255, 255, 0);
        border-bottom-color: #fff;
        border-width: 3px;
        margin-left: -3px;
        transform: scale(0);
        transition: 0.15s cubic-bezier(0.25, 0.8, 0.25, 1)
      }
      &:hover {
        &:after {        
          transform: scale(1)
        }
      }
    }    
    i:before {
      color: #fff;
      margin-left: 0;
      font-size: 22px;
    }
  }
  .menu-hambuger {
    border-radius: 50%;
    padding: 20px;
    cursor: pointer;
    background: -webkit-linear-gradient(left, #5b86e5, #36d1dc); /* For Safari 5.1 to 6.0 */
    background: -o-linear-gradient(right, #5b86e5, #36d1dc); /* For Opera 11.1 to 12.0 */
    background: -moz-linear-gradient(right, #5b86e5, #36d1dc); /* For Firefox 3.6 to 15 */
    background: linear-gradient(to right, #5b86e5, #36d1dc); /* Standard syntax */    
    z-index: 99;
    // transition: all .5s ease-in-out;
    &.has-box-shadow {
      box-shadow: rgba(0,0,0,0.15) -3px 0px 5px 0px;
    }
    i {
      color: #fff
    }
  }
}



.slide-left-leave-active,
.slide-left-enter-active {
  transition: .5s;
}
.slide-left-enter {
  transform: translate(100%, 0);
}
.slide-left-leave-to {
  transform: translate(-100%, 0);
}
.slide-right-leave-active,
.slide-right-enter-active {
  transition: opacity .3s, transform .25s;
  transform-origin: 100% 50%;
}
.slide-right-enter {
  // transform: translate(100%, 0);
  transform: scale(0, 1);
  opacity: 0;
}
.slide-right-leave-to {
  // transform: translate(100%, 0);
  opacity: 0;  
  transform: scale(0, 1);
}
.app-headings {
  font-size: 20px;
  line-height: 23px;
  margin-top: 0;
  margin-bottom: 0;
  font-weight: bold;
}

.app-button {
	background: -webkit-linear-gradient(left, #5b86e5, #36d1dc); /* For Safari 5.1 to 6.0 */
	background: -o-linear-gradient(right, #5b86e5, #36d1dc); /* For Opera 11.1 to 12.0 */
	background: -moz-linear-gradient(right, #5b86e5, #36d1dc); /* For Firefox 3.6 to 15 */
	background: linear-gradient(to right, #5b86e5, #36d1dc); /* Standard syntax */  
	border-radius: 22px !important;
	padding-left: 10px;
	padding-right: 10px;
}

.solution-tab-header{
	.v-tabs-bar__content{
		border-bottom: 2px solid #ccc;
		height: 40px;
	}

	.solution-tab{
		border: 2px solid #ccc;
		border-radius: 5px 5px 0px 0px;
		margin-left: 10px;
		margin-bottom: -1px;
		height: 40px;
		background: #e9e9e9;
		font-weight: 600;
		&.v-tab--active{
			border-bottom: 0px;
			margin-bottom: -1px;
    		background: #f1f1f1;
			color: #5b86e5;
		}
    @media (max-width: 800px){
      &.v-tab:first-child{
        margin-left: 10px !important;
      }
    }
		&:focus{
			box-shadow: none;
		}
	}
}

.modules-container-wrap{
	.v-input--selection-controls{
		margin-top: 5px;
	}
}
</style>