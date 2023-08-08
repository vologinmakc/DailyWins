
import { mount } from '@vue/test-utils'
import HomePage from '@/views/HomePage.vue'

describe('HomePage.vue', () => {
  it('renders the page with the expected layout', () => {
    const wrapper = mount(HomePage)

    // Check for left column content
    const leftColumn = wrapper.find('.left-column')
    expect(leftColumn.exists()).toBe(true)
    expect(leftColumn.text()).toContain('daylywins')
    expect(leftColumn.text()).toContain('Ваш ключ к успешному будущему!')

    // Check for center column content
    const centerColumn = wrapper.find('.center-column')
    expect(centerColumn.exists()).toBe(true)
    expect(centerColumn.text()).toContain('Сегодня')

    // Check for right column content
    const rightColumn = wrapper.find('.right-column')
    expect(rightColumn.exists()).toBe(true)
    expect(rightColumn.text()).toContain('admin@admin.com')
  })
})
