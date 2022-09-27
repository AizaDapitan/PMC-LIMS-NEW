
<template>
 <div class="content-auth">
       <button id="themeMode" type="button" class="btn btn-white rounded-circle ht-50-f wd-50-f position-absolute t-20 r-20 z-index-10 theme-mode mr-0">
          <i data-feather="moon"></i>
        </button>
        <div class="row no-gutters">
          <div class="col-lg-6">
            <div class="signin-hero" data-hero-dark="url(../assets/img/admin-signin-dark.svg)"></div>
          </div>
           <div class="col-lg-6">

               <div class="sign-wrapper">
                   <div class="wd-100p">
                       <h3 class="mg-b-3">Change Password</h3>

                       <span> &nbsp;</span>

                       <p v-if="errors.length">
                         <ul class="list-group">
                           <li v-for="error in errors" :key="error" class="list-group-item list-group-item-danger">{{ error }}</li>
                         </ul>
                       </p>                         

                       <form @submit.prevent="">

                           <div class="form-group">
                               <label for="password">Current Password</label>                                                                 
                               <input type="password" name="password" id="password" v-model="input.current_password" placeholder="Current Password" class="form-control" />
                           </div>

                           <div class="form-group">
                               <label for="password">New Password</label>                                                                 
                               <input :type="passwordVisible ? 'text' : 'password'" name="new_password" id="new_password" v-model="input.new_password" placeholder="New Password" class="form-control" />
                               <button class="visibility" tabindex='-1' @click='togglePasswordVisibility' :arial-label='passwordVisible ? "Hide password" : "Show password"'>
                                 <i class="material-icons">{{ passwordVisible ? "Hide password" : "Show password" }}</i>
                               </button>                                
                           </div>      

                           <div class="form-group">                                                     
                               <label class="control-label"></label><i class="font-red" style="font-size: 16px;font-weight:bold;">(Min. 8, alphanumeric, at least 1 upper case, 1 number and 1 special character) </i>
                           </div> 

                           <div class="form-group">
                               <label for="password">New Confirm Password</label>                                                                 
                               <input type="password" name="new_confirm_password" id="new_confirm_password" v-model="input.new_confirm_password" placeholder="Confirm New Password" class="form-control" />
                           </div>                             
                                                                             


                           <div class="form-group">
                               <button type="submit" class="btn btn-primary btn-sm" @click="updatePassword">Submit</button>
                               &nbsp;
                               <a :href="back" class="btn btn-info btn-sm">Cancel</a>
                               
                           </div>    
                       </form>
                       <div class="cms-footer mg-t-50">
                  <hr>
                  <p class="tx-gray-500 tx-10">Admin Portal v1.0 • Developed by WebFocus Solutions, Inc. 2022</p>
                </div>
              </div>
            </div><!-- sign-wrapper -->
          </div>
        </div>
      </div><!-- content -->
 <toast
   :breakpoints="{ '920px': { width: '100%', right: '0', left: '0' } }"
 ></toast>
</template>

<script  type="application/javascript">
const env_Url = process.env.MIX_APP_URL;
export default {
 name: "Change",
 data() {
   return {
     errors: [],
     result: "",
     back: env_Url + "/deptuser/dashboard",
     input: {
       current_password: "",
       new_password: "",
       new_confirm_password: "",
     },
     passwordVisible:false,
   };
 },
 methods: {
   updatePassword: function (e) 
   {      
     this.errors = [];
     if (!this.input.current_password) 
     {
       this.errors.push("Current Password required.");
     } 
     else if (!this.input.new_password) 
     {
       this.errors.push("New Password required.");
     } 
     else if (!this.input.new_confirm_password) 
     {
       this.errors.push("Confirm New Password required.");
     }        
     else 
     {
           if (this.input.new_password.length < 8) 
           {
             this.errors.push("Your password must be at least 8 characters.");
           } 
           else if (this.input.new_password.search(/\d/) == -1) 
           {
             this.errors.push("Your password must contain at least one digit.");
               
           } 
           else if (this.input.new_password.search(/[a-z]/) == -1) 
           {
             this.errors.push("Your password must contain at least one lower case letter.");
           } 
           else if (this.input.new_password.search(/[A-Z]/) == -1) 
           {
             this.errors.push("Your password must contain at least one upper case letter.");
           }             
           else if (this.input.new_password.search(/[!@#\$%\^&\*_]/) < 0) 
           {
               this.errors.push("Your password must contain at least special char from -[ ! @ # $ % ^ & * _ ]");
           }
           else
           {
             if (this.input.new_password != this.input.new_confirm_password)
             {
                 this.errors.push("Password didn't match.");
             }
             else
             {
               axios
                 .post(env_Url + "/auth/updatePassword", this.input)
                 .then((response) => {
                   if (response.data.result == "Success") 
                   {
                     window.location.href = env_Url + "/deptuser/dashboard";
                   } 
                   else 
                   {
                     this.errors.push("Incorrect Current Password.");
                   }
                 })

                 .catch(function (error) {});
             }
           }
           
     }

     e.preventDefault();
   },

       togglePasswordVisibility () {
           this.passwordVisible = !this.passwordVisible
       },    
       
 },
};
</script>
