<script setup>

import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';



const confirmingUserDeletion = ref(false);

const passwordInput = ref(null);



const form = useForm({

    password: '',

});





const confirmUserDeletion = () => {


    confirmingUserDeletion.value = true;


    nextTick(() => {

        passwordInput.value.focus();

    });


};






const deleteUser = () => {


    form.delete(route('profile.destroy'), {


        preserveScroll: true,


        onSuccess: () => closeModal(),


        onError: () => passwordInput.value.focus(),


        onFinish: () => form.reset(),


    });


};






const closeModal = () => {


    confirmingUserDeletion.value = false;


    form.clearErrors();


    form.reset();


};



</script>





<template>


<section>



    <!-- Header -->


    <header class="mb-6">


        <div class="flex items-center gap-3">


            <div

                class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 text-red-700"

            >

                ⚠️

            </div>



            <div>


                <h2

                    class="text-lg font-semibold text-red-700"

                >

                    Danger Zone

                </h2>



                <p

                    class="text-sm text-gray-500"

                >

                    Permanently remove your account and all related data.

                </p>


            </div>


        </div>


    </header>







    <div

        class="rounded-lg border border-red-200 bg-red-50 p-5"

    >


        <h3

            class="font-semibold text-gray-800"

        >

            Delete Account

        </h3>




        <p

            class="mt-2 text-sm text-gray-600"

        >

            Once your account is deleted, all tickets, comments, and account information will be permanently removed.

            This action cannot be undone.

        </p>





        <button

            @click="confirmUserDeletion"

            class="mt-5 rounded-lg bg-red-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700"

        >

            Delete My Account

        </button>



    </div>









    <!-- Modal -->


    <div

        v-if="confirmingUserDeletion"

        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4"

    >




        <div

            class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl"

        >



            <div class="flex items-center gap-3">


                <div

                    class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100 text-red-600"

                >

                    !

                </div>



                <h2

                    class="text-lg font-bold text-gray-800"

                >

                    Delete Account?

                </h2>


            </div>






            <p

                class="mt-4 text-sm text-gray-600"

            >

                Please enter your password to confirm account deletion.

                This action is permanent and cannot be reversed.

            </p>







            <div class="mt-5">


                <input

                    ref="passwordInput"

                    v-model="form.password"

                    type="password"

                    placeholder="Enter your password"

                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"

                    @keyup.enter="deleteUser"

                />



                <InputError

                    class="mt-2"

                    :message="form.errors.password"

                />


            </div>







            <div

                class="mt-6 flex justify-end gap-3"

            >



                <button

                    @click="closeModal"

                    class="rounded-lg border px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100"

                >

                    Cancel

                </button>






                <button

                    @click="deleteUser"

                    :disabled="form.processing"

                    class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 disabled:opacity-50"

                >

                    Confirm Delete

                </button>



            </div>





        </div>


    </div>





</section>


</template>