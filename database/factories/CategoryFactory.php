<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);
        
        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => $this->faker->paragraph(1, 2),
            'parent_id' => null,
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the category is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the category has a parent.
     */
    public function withParent(): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => Category::factory(),
        ]);
    }

    /**
     * Create a root category (no parent).
     */
    public function root(): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => null,
        ]);
    }

    /**
     * Create a child category with a specific parent.
     */
    public function child(Category $parent): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent->id,
        ]);
    }

    /**
     * Create categories for common task types.
     */
    public function development(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'تطوير البرمجيات',
            'slug' => 'software-development',
            'description' => 'مهام تطوير البرمجيات والبرمجة',
        ]);
    }

    public function design(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'التصميم',
            'slug' => 'design',
            'description' => 'مهام التصميم والرسومات',
        ]);
    }

    public function marketing(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'التسويق',
            'slug' => 'marketing',
            'description' => 'مهام التسويق والدعاية',
        ]);
    }

    public function support(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'الدعم الفني',
            'slug' => 'technical-support',
            'description' => 'مهام الدعم الفني والمساعدة',
        ]);
    }

    public function testing(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'الاختبار',
            'slug' => 'testing',
            'description' => 'مهام الاختبار وضمان الجودة',
        ]);
    }

    public function documentation(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'التوثيق',
            'slug' => 'documentation',
            'description' => 'مهام التوثيق والكتابة التقنية',
        ]);
    }

    public function research(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'البحث',
            'slug' => 'research',
            'description' => 'مهام البحث والتحليل',
        ]);
    }

    public function planning(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'التخطيط',
            'slug' => 'planning',
            'description' => 'مهام التخطيط والاستراتيجية',
        ]);
    }

    public function maintenance(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'الصيانة',
            'slug' => 'maintenance',
            'description' => 'مهام الصيانة والتحديث',
        ]);
    }

    public function training(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'التدريب',
            'slug' => 'training',
            'description' => 'مهام التدريب والتعليم',
        ]);
    }

    /**
     * Create a hierarchy of categories.
     */
    public function createHierarchy(): array
    {
        // Create root categories
        $development = Category::factory()->development()->create();
        $design = Category::factory()->design()->create();
        $marketing = Category::factory()->marketing()->create();
        $support = Category::factory()->support()->create();
        $maintenance = Category::factory()->maintenance()->create();
        $documentation = Category::factory()->documentation()->create();
        $testing = Category::factory()->testing()->create();
        $research = Category::factory()->research()->create();
        $planning = Category::factory()->planning()->create();
        $training = Category::factory()->training()->create();

        // Create subcategories for development
        $frontend = Category::factory()->child($development)->create([
            'name' => 'تطوير الواجهة الأمامية',
            'slug' => 'frontend-development',
            'description' => 'مهام تطوير واجهة المستخدم'
        ]);

        $backend = Category::factory()->child($development)->create([
            'name' => 'تطوير الخلفية',
            'slug' => 'backend-development',
            'description' => 'مهام تطوير الخادم وقاعدة البيانات'
        ]);

        $mobile = Category::factory()->child($development)->create([
            'name' => 'تطوير التطبيقات المحمولة',
            'slug' => 'mobile-development',
            'description' => 'مهام تطوير تطبيقات الهاتف المحمول'
        ]);

        // Create subcategories for design
        $uiDesign = Category::factory()->child($design)->create([
            'name' => 'تصميم واجهة المستخدم',
            'slug' => 'ui-design',
            'description' => 'مهام تصميم واجهة المستخدم'
        ]);

        $uxDesign = Category::factory()->child($design)->create([
            'name' => 'تصميم تجربة المستخدم',
            'slug' => 'ux-design',
            'description' => 'مهام تصميم تجربة المستخدم'
        ]);

        $graphicDesign = Category::factory()->child($design)->create([
            'name' => 'التصميم الجرافيكي',
            'slug' => 'graphic-design',
            'description' => 'مهام التصميم الجرافيكي'
        ]);

        return [
            'development' => $development,
            'design' => $design,
            'marketing' => $marketing,
            'support' => $support,
            'maintenance' => $maintenance,
            'documentation' => $documentation,
            'testing' => $testing,
            'research' => $research,
            'planning' => $planning,
            'training' => $training,
            'frontend' => $frontend,
            'backend' => $backend,
            'mobile' => $mobile,
            'ui_design' => $uiDesign,
            'ux_design' => $uxDesign,
            'graphic_design' => $graphicDesign,
        ];
    }
} 