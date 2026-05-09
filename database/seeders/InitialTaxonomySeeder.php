<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Project;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InitialTaxonomySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['Accounting & Taxation', 'Accounting systems, auditing, taxation, reporting, and financial controls.'],
            ['Agriculture & Food Systems', 'Agriculture, agribusiness, food production, nutrition, and food security.'],
            ['Architecture, Planning & Design', 'Architecture, urban planning, interior design, product design, and spatial studies.'],
            ['Arts & Humanities', 'Creative work, language, culture, history, philosophy, religion, and heritage.'],
            ['Biological & Life Sciences', 'Biology, microbiology, genetics, ecology, biotechnology, and life science research.'],
            ['Business & Management', 'Operations, entrepreneurship, leadership, marketing, strategy, and organizational studies.'],
            ['Communication & Media Studies', 'Journalism, public relations, broadcasting, advertising, film, and digital media.'],
            ['Computer Science & Information Technology', 'Software, hardware, networks, cybersecurity, AI, and digital systems.'],
            ['Data, Statistics & Analytics', 'Statistics, data science, research analytics, business intelligence, and measurement.'],
            ['Earth, Space & Physical Sciences', 'Physics, chemistry, geology, astronomy, materials science, and physical science research.'],
            ['Economics & Development Studies', 'Economic systems, development, trade, markets, poverty, and livelihoods.'],
            ['Education & Learning', 'Teaching, curriculum, learning systems, educational policy, and training.'],
            ['Engineering & Manufacturing', 'Civil, mechanical, electrical, chemical, industrial, and production engineering.'],
            ['Environment, Climate & Sustainability', 'Climate, conservation, environmental management, resources, and sustainability.'],
            ['Finance, Banking & Insurance', 'Finance, banking, investment, risk, insurance, and capital markets.'],
            ['Health, Medicine & Public Health', 'Medicine, nursing, pharmacy, public health, healthcare delivery, and wellbeing.'],
            ['Hospitality, Tourism & Events', 'Tourism, travel, hotels, recreation, events, and destination management.'],
            ['Human Resources & Workplace Studies', 'People management, labor relations, workplace culture, and employee development.'],
            ['International Relations & Security Studies', 'Diplomacy, conflict, peace studies, defense, migration, and global affairs.'],
            ['Law, Governance & Public Policy', 'Law, regulation, governance, public administration, policy, and civic systems.'],
            ['Library, Archives & Information Science', 'Libraries, records, archives, knowledge management, and information access.'],
            ['Linguistics & Languages', 'Language studies, translation, literacy, communication, and applied linguistics.'],
            ['Logistics, Transport & Supply Chain', 'Logistics, procurement, transport systems, warehousing, and distribution.'],
            ['Mathematics & Quantitative Studies', 'Mathematics, modeling, operations research, and quantitative methods.'],
            ['Music, Theatre & Performing Arts', 'Music, theatre, dance, performance, production, and creative practice.'],
            ['Natural Resources & Energy', 'Oil, gas, renewable energy, mining, water, forestry, and resource management.'],
            ['Psychology & Behavioral Science', 'Psychology, behavior, cognition, mental health, and human development.'],
            ['Religion, Ethics & Philosophy', 'Religious studies, ethics, philosophy, values, and belief systems.'],
            ['Social Sciences & Community Development', 'Sociology, anthropology, gender studies, community work, and social change.'],
            ['Sports, Recreation & Wellness', 'Sports science, coaching, fitness, recreation, wellness, and physical education.'],
            ['Visual Arts, Fashion & Creative Industries', 'Fine art, fashion, crafts, photography, graphics, and creative businesses.'],
            ['Vocational, Technical & Applied Skills', 'Technical trades, applied skills, workshop practice, and vocational training.'],
            ['Interdisciplinary & General Studies', 'Projects that combine multiple fields or do not fit a single category.'],
        ];

        foreach ($categories as [$name, $description]) {
            Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'description' => $description]
            );
        }

        $this->mergeLegacyCategories();

        $tags = [
            'Academic Performance',
            'Accounting',
            'Agribusiness',
            'Agriculture',
            'Artificial Intelligence',
            'Audit',
            'Machine Learning',
            'Banking',
            'Biology',
            'Branding',
            'Broadcasting',
            'Budgeting',
            'Business Strategy',
            'Case Study',
            'Child Development',
            'Climate Change',
            'Cloud Computing',
            'Community Development',
            'Community Health',
            'Conflict Resolution',
            'Consumer Behavior',
            'Corporate Governance',
            'Creative Practice',
            'Criminal Justice',
            'Crop Production',
            'Curriculum',
            'Customer Experience',
            'Cybersecurity',
            'Data Analysis',
            'Design',
            'Digital Media',
            'Disability Studies',
            'E-commerce',
            'Economic Development',
            'Education Policy',
            'Energy',
            'Engineering Design',
            'Entrepreneurship',
            'Environmental Management',
            'Ethics',
            'Event Management',
            'Fashion',
            'Finance',
            'Food Security',
            'Gender Studies',
            'Governance',
            'Healthcare Delivery',
            'History',
            'Hospitality',
            'Human Resources',
            'Information System',
            'Innovation',
            'Insurance',
            'International Relations',
            'Language',
            'Law',
            'Leadership',
            'Library Science',
            'Logistics',
            'Manufacturing',
            'Marketing',
            'Mental Health',
            'Microbiology',
            'Mobile Application',
            'Music',
            'Nutrition',
            'Operations Management',
            'Peace Studies',
            'Performance',
            'Pharmacy',
            'Policy Analysis',
            'Procurement',
            'Public Administration',
            'Public Health',
            'Public Relations',
            'Renewable Energy',
            'Research',
            'Risk Management',
            'Rural Development',
            'Small Business',
            'Social Impact',
            'Software',
            'Sports Science',
            'Statistics',
            'Supply Chain',
            'Sustainability',
            'Taxation',
            'Teaching Methods',
            'Theatre',
            'Tourism',
            'Training',
            'Translation',
            'Transport',
            'Urban Planning',
            'Water Resources',
            'Web Development',
            'Wellbeing',
            'Women Empowerment',
            'Youth Development',
            'Automation',
            'Community Impact',
            'UI/UX',
        ];

        foreach ($tags as $name) {
            Tag::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }
    }

    private function mergeLegacyCategories(): void
    {
        $legacyCategories = [
            'technology-computing' => 'computer-science-information-technology',
            'data-analytics' => 'data-statistics-analytics',
            'education-social-sciences' => 'education-learning',
            'engineering-built-environment' => 'engineering-manufacturing',
            'environment-sustainability' => 'environment-climate-sustainability',
            'health-life-sciences' => 'health-medicine-public-health',
            'law-governance' => 'law-governance-public-policy',
        ];

        foreach ($legacyCategories as $oldSlug => $newSlug) {
            $oldCategory = Category::where('slug', $oldSlug)->first();
            $newCategory = Category::where('slug', $newSlug)->first();

            if (! $oldCategory || ! $newCategory || $oldCategory->is($newCategory)) {
                continue;
            }

            Project::where('category_id', $oldCategory->id)->update([
                'category_id' => $newCategory->id,
            ]);

            $oldCategory->delete();
        }
    }
}
