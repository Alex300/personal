-- ---
-- Completely removes Personal module data
-- ---
DROP TABLE IF EXISTS `cot_personal_categories`,
                     `cot_personal_staff`,
                     `cot_personal_education_levels`,
                     `cot_personal_languages`,

                     `cot_personal_empl_profiles`,

                     `cot_personal_vacancies`,
                     `cot_personal_vacancies_employment`,
                     `cot_personal_vacancies_link_cot_personal_categories`,
                     `cot_personal_vacancies_link_cot_personal_staff`,
                     `cot_personal_vacancies_schedule`,

                     `cot_personal_resumes`,
                     `cot_personal_resumes_education`,
                     `cot_personal_resumes_employment`,
                     `cot_personal_resumes_experience`,
                     `cot_personal_resumes_lang_levels`,
                     `cot_personal_resumes_link_cot_city`,
                     `cot_personal_resumes_link_cot_personal_categories`,
                     `cot_personal_resumes_link_cot_personal_staff`,
                     `cot_personal_resumes_recommendations`,
                     `cot_personal_resumes_schedule`;