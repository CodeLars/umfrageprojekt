<template>
  <h2>Umfrage erstellen</h2>
  <div>
    <form id="survey-create-form" @submit.prevent="onSubmitSurvey()">
      <div>
        <label for="title">Title</label>
        <input type="text" id="title" v-model="form.title" />
      </div>
      <div>
        <label for="category">Umfragenkategorie:</label>
        <select id="category" :disabled="categories.length === 0">
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.cat_name }}
          </option>
        </select>
      </div>
      <div>
        <label for="start_date">Start Date</label>
        <input type="date" id="start_date" />
      </div>
      <div>
        <label for="end_date">End Date</label>
        <input type="date" id="end_date" />
      </div>

      <!-- status -->
      <div>
        <label for="status">Status</label>
        <select id="status">
          <option v-bind:key="option" :value="option" v-for="option in surveyStateOptions">
            {{ option }}
          </option>
        </select>
      </div>
      <!-- topic (comment/description) input -->

      <!-- startdatum / enddatum  -->

      <!-- CATEGORY DROPDOWN -->

      <!-- questions list -->
      <div>
        <h3>
          Questions:
        </h3>
        <pre>
          {{ form.questions }}
        </pre>
      </div>
      <QuestionForm @question_added="onAddQuestion" />

      <button type="submit">Umfrage einreichen</button>
    </form>
  </div>
</template>

<script setup lang="ts">
import QuestionForm from '@/components/survey/questionForm.vue'
import {ref} from 'vue'
import {SurveyState, type SurveyQuestion, type Category} from '@/types/survey'
import {$fetch, ofetch} from 'ofetch'

const questionForm = ref<Object>({})

const API_URL = import.meta.env.VITE_API_URL

const form = ref({
  title: '',
  questions: [] as SurveyQuestion[]
});

const categories = ref<Category[]>([]);

const surveyStateOptions = Object.values(SurveyState)

function onAddQuestion(question: SurveyQuestion) {
  console.log(question)
  if (!question) return
  form.value.questions.push(JSON.parse(JSON.stringify(question)))
}

async function loadCategories() {
  const response = await ofetch('/category', {
    baseURL: API_URL,
    method: 'GET'
  });
  categories.value = response || [];

}

async function onSubmitSurvey() {
  console.log(form.value)
  const response = await ofetch('/survey', {
    baseURL: API_URL,
    method: 'POST',
    body: JSON.stringify(form.value)
  })
  console.log(response)
  if (response.ok) {
    alert('Survey created')
  } else {
    alert('Error creating survey')
  }
}

loadCategories();
</script>
