<script setup>

import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';


const passwordInput = ref(null);

const currentPasswordInput = ref(null);



const form = useForm({

    current_password: '',

    password: '',

    password_confirmation: '',

});




const updatePassword = () => {


    form.put(route('password.update'), {


        preserveScroll: true,


        onSuccess: () => {


            form.reset();


        },


        onError: () => {


            if (form.errors.password) {


                form.reset(
                    'password',
                    'password_confirmation'
                );


                passwordInput.value.focus();

            }



            if (form.errors.current_password) {


                form.reset(
                    'current_password'
                );


                currentPasswordInput.value.focus();


            }


        },


    });


};



</script>



<template>


<section>



    <!-- Header -->


    <header class="mb-6">


        <div class="flex items-center gap-3">


            <div

                class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 text-green-700"

            >

                🔒

            </div>



            <div>


                <h2

                    class="text-lg font-semibold text-gray-800"

                >

                    Update Password

                </h2>



                <p

                    class="text-sm text-gray-500"

                >

                    Keep your account secure by using a strong password.

                </p>



            </div>



        </div>



    </header>









    <form

        @submit.prevent="updatePassword"

        class="space-y-6"

    >







        <!-- Current Password -->


        <div>


            <InputLabel

                for="current_password"

                value="Current Password"

                class="mb-2"

            />



            <input

                id="current_password"

                ref="currentPasswordInput"

                v-model="form.current_password"

                type="password"

                autocomplete="current-password"

                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"

            />



            <InputError

                class="mt-2"

                :message="form.errors.current_password"

            />


        </div>









        <!-- New Password -->


        <div>


            <InputLabel

                for="password"

                value="New Password"

                class="mb-2"

            />



            <input

                id="password"

                ref="passwordInput"

                v-model="form.password"

                type="password"

                autocomplete="new-password"

                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"

            />



            <InputError

                class="mt-2"

                :message="form.errors.password"

            />


        </div>









        <!-- Confirm Password -->


        <div>


            <InputLabel

                for="password_confirmation"

                value="Confirm New Password"

                class="mb-2"

            />



            <input

                id="password_confirmation"

                v-model="form.password_confirmation"

                type="password"

                autocomplete="new-password"

                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"

            />



            <InputError

                class="mt-2"

                :message="form.errors.password_confirmation"

            />


        </div>









        <!-- Button -->


        <div class="flex items-center gap-4">


            <button

                type="submit"

                :disabled="form.processing"

                class="rounded-lg bg-green-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-green-700 disabled:opacity-50"

            >

                Update Password

            </button>





            <Transition

                enter-active-class="transition ease-in-out"

                enter-from-class="opacity-0"

                leave-active-class="transition ease-in-out"

                leave-to-class="opacity-0"

            >


                <p

                    v-if="form.recentlySuccessful"

                    class="text-sm text-green-600"

                >

                    Password updated.

                </p>


            </Transition>




        </div>







    </form>





</section>


</template>